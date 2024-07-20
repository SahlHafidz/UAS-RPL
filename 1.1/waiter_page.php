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

// Query untuk mengambil informasi meja yang tersedia dan tidak sedang dipesan
$sql = "
    SELECT t.id, t.table_number 
    FROM tables t 
    LEFT JOIN orders o ON t.id = o.table_id AND o.paid = FALSE 
    WHERE t.status = 'available' AND o.id IS NULL
    ORDER BY t.table_number ASC
";
$result = $conn->query($sql);

// Buat array untuk menyimpan hasil query
$availableTables = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableTables[$row['id']] = $row['table_number'];
    }
}

// Query untuk mengambil data menu dari tabel menu_items
$sql = "SELECT id, name, category FROM menu_items";
$result = $conn->query($sql);

// Buat array untuk menyimpan hasil query
$menuItems = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[$row['id']] = array('name' => $row['name'], 'category' => $row['category']);
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
                            <label for="category1">Kategori:</label>
                            <select class="form-control category-select" id="category1" data-menu-item="1" required>
                                <option value="all">Semua Menu</option>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="camilan">Camilan</option> 
                            </select>
                        </div>
                        <div class="col">
                            <label for="menuItem1">Pilih Menu:</label>
                            <select class="form-control menu-select" id="menuItem1" name="menuItems[1][menuId]" required>
                                <option value="">-- Pilih Menu --</option>
                                <?php
                                // Loop untuk menampilkan opsi menu dari array $menuItems
                                foreach ($menuItems as $id => $item) {
                                    echo "<option value='{$id}' data-category='{$item['category']}'>{$item['name']}</option>";
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
                                <label for="category${menuItemCount}">Kategori:</label>
                                <select class="form-control category-select" id="category${menuItemCount}" data-menu-item="${menuItemCount}" required>
                                    <option value="all">Semua Menu</option>
                                    <option value="makanan">Makanan</option>
                                    <option value="minuman">Minuman</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="menuItem${menuItemCount}">Pilih Menu:</label>
                                <select class="form-control menu-select" id="menuItem${menuItemCount}" name="menuItems[${menuItemCount}][menuId]" required>
                                    <option value="">-- Pilih Menu --</option>
                                    <?php
                                    // Loop untuk menampilkan opsi menu dari array $menuItems
                                    foreach ($menuItems as $id => $item) {
                                        echo "<option value='{$id}' data-category='{$item['category']}'>{$item['name']}</option>";
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

            // Fungsi untuk menghapus item menu dari formulir
            window.removeMenuItem = function(button) {
                $(button).closest('.menu-item').remove();
            };

            // Fungsi untuk memperbarui opsi menu berdasarkan kategori yang dipilih
            function updateMenuOptions(menuItem) {
                var category = $(`#category${menuItem}`).val();
                var menuSelect = $(`#menuItem${menuItem}`);

                menuSelect.find('option').each(function() {
                    var option = $(this);
                    if (category === 'all' || option.data('category') === category) {
                        option.show();
                    } else {
                        option.hide();
                    }
                });

                // Pilih ulang opsi kosong
                menuSelect.val('');
            }

            // Event listener untuk perubahan kategori
            $(document).on('change', '.category-select', function() {
                var menuItem = $(this).data('menu-item');
                updateMenuOptions(menuItem);
            });

            // Inisialisasi opsi menu berdasarkan kategori yang dipilih saat pertama kali dimuat
            $('.category-select').each(function() {
                var menuItem = $(this).data('menu-item');
                updateMenuOptions(menuItem);
            });
        });
    </script>
</body>
</html>
