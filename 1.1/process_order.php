<?php
session_start();
include 'config.php'; // File koneksi ke database

// Periksa apakah pelayan sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelayan') {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $tableId = $_POST['table'];
    $menuItems = $_POST['menuItems'];

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

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Simpan pesanan ke tabel orders
        $sql = "INSERT INTO orders (customer_name, table_id, status, paid) VALUES (?, ?, 'pending', FALSE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $customerName, $tableId);
        $stmt->execute();
        $orderId = $stmt->insert_id; // Dapatkan ID pesanan yang baru saja disimpan

        // Simpan setiap item pesanan ke tabel order_items dan kurangi stok di tabel menu_items
        foreach ($menuItems as $item) {
            $menuItemId = $item['menuId'];
            $quantity = $item['quantity'];

            // Simpan item pesanan
            $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $orderId, $menuItemId, $quantity);
            $stmt->execute();

            // Kurangi stok menu
            $sql = "UPDATE menu_items SET stock = stock - ? WHERE id = ? AND stock >= ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iii', $quantity, $menuItemId, $quantity);
            $stmt->execute();
        }

        // Commit transaksi
        $conn->commit();

        // Redirect ke halaman pelayan dengan pesan sukses
        header("Location: waiter_page.php?status=success");
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
    }

    // Tutup koneksi
    $stmt->close();
    $conn->close();
}
?>
