<?php
// Database connection
$servername = "sql310.infinityfree.com";
$username = "if0_37243588";
$password = "NYef1AKV5oB";
$dbname = "if0_37243588_akash_communication";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions to add/delete categories/products
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
        $conn->query($sql);
    }

    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        $sql = "DELETE FROM categories WHERE id = $category_id";
        $conn->query($sql);
    }

    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $category_id = $_POST['category_id'];
        $product_price = $_POST['product_price'];
        $product_stock = $_POST['product_stock'];
        $sql = "INSERT INTO products (name, category_id, price, stock) VALUES ('$product_name', $category_id, '$product_price', '$product_stock')";
        $conn->query($sql);
    }

    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $sql = "DELETE FROM products WHERE id = $product_id";
        $conn->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Akash Communication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }
        .card {
            margin-top: 20px;
        }
        .product-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php
        require 'nav.php';
    ?>

    <div class="container mt-5">
        <h2>Product Categories</h2>
        <div class="row">
            <?php
            $sql = "SELECT * FROM categories";
            $categories = $conn->query($sql);
            while ($category = $categories->fetch_assoc()) {
                echo '<div class="col-md-4">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $category['name'] . '</h5>';
                echo '<ul class="list-group list-group-flush">';

                $sql = "SELECT * FROM products WHERE category_id=" . $category['id'];
                $products = $conn->query($sql);
                while ($product = $products->fetch_assoc()) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    echo $product['name'] . ' - ₹' . $product['price'] . '(Stock: ' . $product['stock'] . ')';
                    echo '<form method="POST" class="d-inline">';
                    echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
                    echo '<button type="submit" name="delete_product" class="btn btn-sm btn-danger">Delete</button>';
                    echo '</form>';
                    echo '</li>';
                }
                
                echo '</ul>';
                echo '<div class="product-actions">';
                echo '<form method="POST" class="mb-2">';
                echo '<input type="hidden" name="category_id" value="' . $category['id'] . '">';
                echo '<input type="text" name="product_name" class="form-control mb-2" placeholder="New Product Name" required>';
                echo '<input type="number" name="product_price" class="form-control mb-2" placeholder="Product Price" required>';
                echo '<input type="number" name="product_stock" class="form-control mb-2" placeholder="Stock Quantity" required>';
                echo '<button type="submit" name="add_product" class="btn btn-primary btn-block">Add Product</button>';
                echo '</form>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="category_id" value="' . $category['id'] . '">';
                echo '<button type="submit" name="delete_category" class="btn btn-danger btn-block">Delete Category</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <h2 class="mt-5">Add New Category</h2>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
            </div>
            <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
