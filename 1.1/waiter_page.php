<?php
// Halaman awal setelah login pelayan

// Secara langsung arahkan ke halaman ini setelah login
session_start(); // Pastikan session dimulai

// Periksa apakah pelayan sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelayan') {
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

// Query untuk mengambil informasi meja yang tersedia
$sql = "SELECT id, table_number FROM tables WHERE status = 'available'";
$result = $conn->query($sql);

// Buat array untuk menyimpan hasil query
$availableTables = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableTables[$row['id']] = $row['table_number'];
    }
}

// Query untuk mengambil data menu dari tabel menu_items
$sql = "SELECT id, name FROM menu_items";
$result = $conn->query($sql);

// Buat array untuk menyimpan hasil query
$menuItems = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[$row['id']] = $row['name'];
    }
}

// Tutup koneksi
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pencatatan Pesanan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pencatatan Pesanan</h2>
        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="alert alert-success">Pesanan berhasil dicatat.</div>';
        }
        ?>
        <form action="process_order.php" method="POST" id="orderForm">
            <div class="form-group">
                <label for="customerName">Nama Pelanggan:</label>
                <input type="text" class="form-control" id="customerName" name="customerName" required>
            </div>
            <div class="form-group">
                <label for="table">Pilih Meja:</label>
                <select class="form-control" id="table" name="table" required>
                    <?php
                    // Loop untuk menampilkan opsi meja dari array $availableTables
                    foreach ($availableTables as $id => $tableNumber) {
                        echo "<option value='{$id}'>Meja {$tableNumber}</option>";
                    }
                    ?>
                </select>
            </div>
            <div id="menuItems">
                <!-- Input untuk menu pertama -->
                <div class="menu-item mb-3">
                    <div class="form-row align-items-center">
                        <div class="col">
                            <label for="menuItem1">Pilih Menu:</label>
                            <select class="form-control" id="menuItem1" name="menuItems[1][menuId]" required>
                                <?php
                                // Loop untuk menampilkan opsi menu dari array $menuItems
                                foreach ($menuItems as $id => $name) {
                                    echo "<option value='{$id}'>{$name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="quantity1">Jumlah:</label>
                            <input type="number" class="form-control" id="quantity1" name="menuItems[1][quantity]" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeMenuItem(this)">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="addMenuItem">Tambah Menu</button>
            <button type="submit" class="btn btn-success">Pesan</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var menuItemCount = 1;

            // Tambahkan menu baru saat tombol "Tambah Menu" diklik
            $('#addMenuItem').click(function() {
                menuItemCount++;
                var newItem = `
                    <div class="menu-item mb-3">
                        <div class="form-row align-items-center">
                            <div class="col">
                                <label for="menuItem${menuItemCount}">Pilih Menu:</label>
                                <select class="form-control" id="menuItem${menuItemCount}" name="menuItems[${menuItemCount}][menuId]" required>
                                    <?php
                                    // Loop untuk menampilkan opsi menu dari array $menuItems
                                    foreach ($menuItems as $id => $name) {
                                        echo "<option value='{$id}'>{$name}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="quantity${menuItemCount}">Jumlah:</label>
                                <input type="number" class="form-control" id="quantity${menuItemCount}" name="menuItems[${menuItemCount}][quantity]" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeMenuItem(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                `;
                $('#menuItems').append(newItem);
            });
        });

        // Fungsi untuk menghapus item menu dari formulir
        function removeMenuItem(button) {
            $(button).closest('.menu-item').remove();
        }
    </script>
</body>
</html>
