<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelayan') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RM_unikom";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM reservations WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: reservasi.php?status=deleted");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $name = $_POST['name'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $phone_number = $_POST['phone_number'];

        $sql = "INSERT INTO reservations (name, date, time, phone_number) VALUES ('$name', '$date', '$time', '$phone_number')";

        if ($conn->query($sql) === TRUE) {
            header("Location: reservasi.php?status=success");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$sql = "SELECT * FROM reservations ORDER BY date, time";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservasi Meja</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Restoran</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="waiter_page.php">Pemesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservasi.php">Reservasi Meja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kelola.php">Kelola Meja</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Reservasi Meja</h2>
        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="alert alert-success">Reservasi berhasil dicatat.</div>';
        } elseif (isset($_GET['status']) && $_GET['status'] == 'deleted') {
            echo '<div class="alert alert-success">Reservasi berhasil dihapus.</div>';
        }
        ?>
        <form action="reservasi.php" method="POST">
            <div class="form-group">
                <label for="name">Nama:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="date">Tanggal:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="time">Waktu:</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>
            <div class="form-group">
                <label for="phone_number">No HP:</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <button type="submit" class="btn btn-success">Reservasi</button>
        </form>
        <h3 class="mt-5">Daftar Reservasi</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['date']}</td>";
                        echo "<td>{$row['time']}</td>";
                        echo "<td>{$row['phone_number']}</td>";
                        echo "<td>";
                        echo "<form action='reservasi.php' method='POST' class='d-inline'>";
                        echo "<input type='hidden' name='id' value='{$row['id']}'>";
                        echo "<button type='submit' name='delete' class='btn btn-danger btn-sm'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada reservasi</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
