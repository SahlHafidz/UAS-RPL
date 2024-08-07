<?php
session_start();
include 'db_connect.php'; // File koneksi ke database

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

// Ambil data dari form
$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Update status pesanan
$sql = "UPDATE orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $order_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Redirect kembali ke halaman koki
header("Location: chef_page.php");
exit();
