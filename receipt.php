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

// Ambil ID pesanan dari query string
$order_id = $_GET['order_id'];

// Query untuk mendapatkan informasi pesanan
$sql = "SELECT orders.id, orders.customer_name, GROUP_CONCAT(menu_items.name SEPARATOR ', ') AS items, SUM(order_items.quantity * menu_items.price) AS total
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN menu_items ON order_items.menu_item_id = menu_items.id
        WHERE orders.id = ?
        GROUP BY orders.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Nota Pembayaran</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Nama Pelanggan: <?php echo htmlspecialchars($order['customer_name']); ?></h5>
                <p class="card-text">Pesanan: <?php echo htmlspecialchars($order['items']); ?></p>
                <p class="card-text">Total: Rp <?php echo htmlspecialchars($order['total']); ?></p>
            </div>
        </div>
        <a href="cashier_page.php" class="btn btn-primary mt-3">Kembali</a>
    </div>
</body>
</html>
