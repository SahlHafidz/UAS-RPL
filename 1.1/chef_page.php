<?php
session_start();

// Periksa apakah koki sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'koki') {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

// Koneksi ke database
$servername = "localhost";  // Ganti dengan nama server Anda jika berbeda
$username = "root";         // Ganti dengan username database Anda
$password = "";             // Ganti dengan password database Anda
$dbname = "RM_unikom";      // Nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data pesanan yang belum selesai
$sql = "SELECT orders.id, orders.customer_name, orders.table_id, orders.status, menu_items.name AS menu_name, order_items.quantity 
        FROM orders 
        JOIN order_items ON orders.id = order_items.order_id 
        JOIN menu_items ON order_items.menu_item_id = menu_items.id 
        WHERE orders.status IN ('pending', 'in_progress')";
$result = $conn->query($sql);

// Buat array untuk menyimpan hasil query
$orders = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[$row['id']]['customer_name'] = $row['customer_name'];
        $orders[$row['id']]['table_id'] = $row['table_id'];
        $orders[$row['id']]['status'] = $row['status'];
        $orders[$row['id']]['items'][] = array('menu_name' => $row['menu_name'], 'quantity' => $row['quantity']);
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pesanan untuk Koki</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Koki</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="chef_page.php">Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_menu.php">Kelola Menu</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php">Logout</a>
            </form>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Daftar Pesanan</h2>
        <?php if (!empty($orders)): ?>
            <div class="list-group">
                <?php foreach ($orders as $orderId => $order): ?>
                    <div class="list-group-item">
                        <h5>Pesanan ID: <?php echo $orderId; ?></h5>
                        <p>Nama Pelanggan: <?php echo $order['customer_name']; ?></p>
                        <p>Meja: <?php echo $order['table_id']; ?></p>
                        <p>Status: <?php echo $order['status']; ?></p>
                        <ul>
                            <?php foreach ($order['items'] as $item): ?>
                                <li><?php echo $item['menu_name']; ?> - Jumlah: <?php echo $item['quantity']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <form action="update_order_status.php" method="POST" class="form-inline">
                            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                            <select name="status" class="form-control mb-2 mr-sm-2" required>
                                <option value="in_progress" <?php echo $order['status'] == 'in_progress' ? 'selected' : ''; ?>>Sedang Diproses</option>
                                <option value="ready" <?php echo $order['status'] == 'ready' ? 'selected' : ''; ?>>Siap Diantar</option>
                            </select>
                            <button type="submit" class="btn btn-primary mb-2">Update Status</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Tidak ada pesanan yang belum selesai.</p>
        <?php endif; ?>
    </div>
</body>
</html>
