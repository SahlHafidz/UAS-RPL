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
    if (isset($_POST['add'])) {
        $tableNumber = $_POST['table_number'];
        $sql = "INSERT INTO tables (table_number, status) VALUES ('$tableNumber', 'available')";

        if ($conn->query($sql) === TRUE) {
            header("Location: kelola.php?status=added");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $tableNumber = $_POST['table_number'];
        $status = $_POST['status'];
        $sql = "UPDATE tables SET table_number='$tableNumber', status='$status' WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            header("Location: manage_tables.php?status=edited");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM tables WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            header("Location: manage_tables.php?status=deleted");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$sql = "SELECT * FROM tables ORDER BY table_number";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Meja</title>
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
        <h2>Kelola Meja</h2>
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'added') {
                echo '<div class="alert alert-success">Meja berhasil ditambahkan.</div>';
            } elseif ($_GET['status'] == 'edited') {
                echo '<div class="alert alert-success">Meja berhasil diubah.</div>';
            } elseif ($_GET['status'] == 'deleted') {
                echo '<div class="alert alert-success">Meja berhasil dihapus.</div>';
            }
        }
        ?>
        <form action="kelola.php" method="POST">
            <div class="form-group">
                <label for="table_number">Nomor Meja:</label>
                <input type="text" class="form-control" id="table_number" name="table_number" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Tambah Meja</button>
        </form>
        <h3 class="mt-5">Daftar Meja</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nomor Meja</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['table_number']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td>";
                        echo "<form action='manage_tables.php' method='POST' class='d-inline'>";
                        echo "<input type='hidden' name='id' value='{$row['id']}'>";
                        echo "<input type='text' name='table_number' value='{$row['table_number']}' required>";
                        echo "<select name='status' required>";
                        echo "<option value='available'" . ($row['status'] == 'available' ? ' selected' : '') . ">Available</option>";
                        echo "<option value='occupied'" . ($row['status'] == 'occupied' ? ' selected' : '') . ">Occupied</option>";
                        echo "</select>";
                        echo "<button type='submit' name='edit' class='btn btn-warning btn-sm'>Edit</button>";
                        echo "<button type='submit' name='delete' class='btn btn-danger btn-sm'>Hapus</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Tidak ada meja</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
