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

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Simpan pesanan ke tabel orders
        $sql = "INSERT INTO orders (customer_name, table_id, status) VALUES ('$customerName', '$tableId', 'pending')";
        if ($conn->query($sql) === TRUE) {
            $orderId = $conn->insert_id; // Dapatkan ID pesanan yang baru saja disimpan

            // Simpan setiap item pesanan ke tabel order_items dan kurangi stok di tabel menu_items
            foreach ($menuItems as $item) {
                $menuItemId = $item['menuId'];
                $quantity = $item['quantity'];

                // Simpan item pesanan
                $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES ('$orderId', '$menuItemId', '$quantity')";
                if ($conn->query($sql) !== TRUE) {
                    throw new Exception("Error: " . $conn->error);
                }

                // Kurangi stok menu
                $sql = "UPDATE menu_items SET stock = stock - $quantity WHERE id = '$menuItemId' AND stock >= $quantity";
                if ($conn->query($sql) !== TRUE) {
                    throw new Exception("Error: " . $conn->error);
                }
            }

            // Commit transaksi
            $conn->commit();

            // Redirect ke halaman pelayan dengan pesan sukses
            header("Location: waiter_page.php?status=success");
        } else {
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Terjadi kesalahan: " . $e->getMessage();
    }

    // Tutup koneksi
    $conn->close();
}
?>
