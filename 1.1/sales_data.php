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

// Query untuk mengambil data penjualan
$sql = "
    SELECT 
        o.id AS order_id,
        o.customer_name,
        o.created_at,
        p.amount,
        p.payment_method
    FROM 
        orders o
    JOIN 
        payments p ON o.id = p.order_id
    ORDER BY 
        o.created_at DESC
";

$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Data Penjualan</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['order_id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['created_at']}</td>
                            <td>{$row['amount']}</td>
                            <td>{$row['payment_method']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data penjualan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="owner.php" class="btn btn-primary">Kembali</a>
    </div>
</body>
</html>
