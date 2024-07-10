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

// Ambil data dari form
$order_id = $_POST['order_id'];
$total = $_POST['total'];
$payment_method = $_POST['payment_method'];

// Mulai transaksi
$conn->begin_transaction();

try {
    // Update status pesanan
    $sql = "UPDATE orders SET status = 'paid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    
    // Catat detail pembayaran
    $sql = "INSERT INTO payments (order_id, amount, payment_method) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ids', $order_id, $total, $payment_method);
    $stmt->execute();
    
    // Commit transaksi
    $conn->commit();

    // Redirect ke halaman nota
    header("Location: receipt.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $conn->rollback();
    echo "Terjadi kesalahan: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?>
