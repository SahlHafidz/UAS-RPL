<?php
session_start();

// Periksa apakah pengguna adalah kasir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RM_unikom";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan pesanan yang siap dibayar
$sql = "SELECT orders.id, orders.customer_name, GROUP_CONCAT(menu_items.name SEPARATOR ', ') AS items, SUM(order_items.quantity * menu_items.price) AS total
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN menu_items ON order_items.menu_item_id = menu_items.id
        WHERE orders.status = 'ready' AND orders.paid = FALSE
        GROUP BY orders.id";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Halaman Kasir</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pesanan yang Siap Dibayar</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Pesanan</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['items']); ?></td>
                    <td><?php echo htmlspecialchars($order['total']); ?></td>
                    <td>
                        <form action="process_payment.php" method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="total" value="<?php echo $order['total']; ?>">
                            <button type="submit" name="payment_method" value="cash" class="btn btn-primary btn-sm">Bayar Tunai</button>
                            <button type="submit" name="payment_method" value="credit_card" class="btn btn-secondary btn-sm">Kartu Kredit</button>
                            <button type="submit" name="payment_method" value="debit_card" class="btn btn-secondary btn-sm">Kartu Debit</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
