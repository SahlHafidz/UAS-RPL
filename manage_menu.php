<?php
session_start();

// Periksa apakah koki sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'koki') {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "RM_unikom";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambahkan menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $sql = "INSERT INTO menu_items (name, price, stock, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdis", $name, $price, $stock, $category);

    $stmt->execute();
    $stmt->close();
}

// Hapus menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_menu'])) {
    $menu_id = $_POST['menu_id'];

    $sql = "DELETE FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menu_id);

    $stmt->execute();
    $stmt->close();
}

// Edit menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_menu'])) {
    $menu_id = $_POST['menu_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $sql = "UPDATE menu_items SET name = ?, price = ?, stock = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisi", $name, $price, $stock, $category, $menu_id);

    $stmt->execute();
    $stmt->close();
}

// Ambil data menu
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

$menu_items = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Koki</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="chef_page.php">Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_menu.php">Kelola Menu</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php">Logout</a>
            </form>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Kelola Menu</h2>
        <form action="manage_menu.php" method="POST" id="addMenuForm">
            <div class="form-group">
                <label for="name">Nama Menu:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Harga:</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="stock">Stok:</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori:</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_menu">Tambahkan Menu</button>
        </form>
        <br>
        <h3>Daftar Menu</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu_items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['price']; ?></td>
                        <td><?php echo $item['stock']; ?></td>
                        <td><?php echo $item['category']; ?></td>
                        <td>
                            <button class="btn btn-warning edit-btn" data-id="<?php echo $item['id']; ?>" data-name="<?php echo $item['name']; ?>" data-price="<?php echo $item['price']; ?>" data-stock="<?php echo $item['stock']; ?>" data-category="<?php echo $item['category']; ?>">Edit</button>
                            <form action="manage_menu.php" method="POST" class="d-inline">
                                <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_menu" class="btn btn-danger ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Form Edit -->
        <div id="editMenuFormContainer" style="display:none;">
            <h3>Edit Menu</h3>
            <form action="manage_menu.php" method="POST" id="editMenuForm">
                <input type="hidden" name="menu_id" id="editMenuId">
                <div class="form-group">
                    <label for="editName">Nama Menu:</label>
                    <input type="text" class="form-control" id="editName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="editPrice">Harga:</label>
                    <input type="number" step="0.01" class="form-control" id="editPrice" name="price" required>
                </div>
                <div class="form-group">
                    <label for="editStock">Stok:</label>
                    <input type="number" class="form-control" id="editStock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="editCategory">Kategori:</label>
                    <input type="text" class="form-control" id="editCategory" name="category" required>
                </div>
                <button type="submit" class="btn btn-success" name="update_menu">Update Menu</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-btn');
            const editFormContainer = document.getElementById('editMenuFormContainer');
            const editForm = document.getElementById('editMenuForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const price = button.getAttribute('data-price');
                    const stock = button.getAttribute('data-stock');
                    const category = button.getAttribute('data-category');

                    document.getElementById('editMenuId').value = id;
                    document.getElementById('editName').value = name;
                    document.getElementById('editPrice').value = price;
                    document.getElementById('editStock').value = stock;
                    document.getElementById('editCategory').value = category;

                    editFormContainer.style.display = 'block';
                });
            });
        });
    </script>
</body>
</html>
