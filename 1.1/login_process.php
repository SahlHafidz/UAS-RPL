<?php
session_start();
include 'config.php'; // File ini berisi koneksi database

$username = $_POST['username'];
$password = $_POST['password'];

// Menggunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $role);
    $stmt->fetch();
    
    // Set session
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = $role;
    
    // Redirect based on role
    if ($role == 'pelayan') {
        header("Location: waiter_page.php");
    } elseif ($role == 'koki') {
        header("Location: chef_page.php");
    } elseif ($role == 'kasir') {
        header("Location: cashier_page.php");
    }
    elseif ($role == 'owner') {
        header("Location: owner_page.php");
    }
    
    exit();
} else {
    echo "Username atau password salah";
}
$stmt->close();
$conn->close();
?>
