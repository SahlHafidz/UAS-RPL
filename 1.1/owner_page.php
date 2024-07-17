<?php
session_start();

// Periksa apakah pengguna adalah pemilik restoran
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Ganti dengan algoritma hashing yang lebih aman dalam produksi
    $role = $_POST['role'];

    // Periksa apakah username sudah ada
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username sudah ada.";
    } else {
        // Masukkan data karyawan baru ke dalam tabel users
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $password, $role);

        if ($stmt->execute()) {
            echo "Akun karyawan berhasil dibuat.";
        } else {
            echo "Terjadi kesalahan: " . $conn->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Owner Dashboard</h2>
        <form action="owner.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="pelayan">Pelayan</option>
                    <option value="koki">Koki</option>
                    <option value="kasir">Kasir</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="create_user">Buat Akun</button>
        </form>
        <br>
        <a href="sales_data.php" class="btn btn-info">Lihat Data Penjualan</a>
    </div>
</body>
</html>
