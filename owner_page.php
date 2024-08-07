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

// Buat akun karyawan baru
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
        $msg = "Username sudah ada.";
    } else {
        // Masukkan data karyawan baru ke dalam tabel users
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $password, $role);

        if ($stmt->execute()) {
            $msg = "Akun karyawan berhasil dibuat.";
        } else {
            $msg = "Terjadi kesalahan: " . $conn->error;
        }
    }

    $stmt->close();
}

// Hapus akun karyawan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        $msg = "Akun karyawan berhasil dihapus.";
    } else {
        $msg = "Terjadi kesalahan: " . $conn->error;
    }

    $stmt->close();
}

// Ambil daftar karyawan
$sql = "SELECT id, username, role FROM users WHERE role != 'owner'";
$result = $conn->query($sql);

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
        <?php
        if (isset($msg)) {
            echo "<div class='alert alert-info'>{$msg}</div>";
        }
        ?>
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
        <h3>Daftar Karyawan</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['role']}</td>";
                        echo "<td>";
                        echo "<form action='owner.php' method='POST' style='display:inline-block;'>";
                        echo "<input type='hidden' name='user_id' value='{$row['id']}'>";
                        echo "<button type='submit' class='btn btn-danger btn-sm' name='delete_user'>Hapus</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Tidak ada karyawan</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <a href="sales_data.php" class="btn btn-info">Lihat Data Penjualan</a>
    </div>
</body>
</html>
