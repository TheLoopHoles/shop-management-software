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

// Fetch overall stock information
$sql = "SELECT products.name, categories.name AS category_name, SUM(stock) AS total_stock 
        FROM products 
        INNER JOIN categories ON products.category_id = categories.id 
        GROUP BY products.name, categories.name";
$overall_stock = $conn->query($sql);

// Fetch category-wise stock information
$sql = "SELECT categories.name AS category_name, SUM(stock) AS total_stock 
        FROM products 
        INNER JOIN categories ON products.category_id = categories.id 
        GROUP BY categories.name";
$category_stock = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Akash Communication</title>
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
        .stock-table {
            margin-top: 20px;
        }
        .category-stock {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <?php
        require 'nav.php';
    ?>

    <div class="container mt-5">
        <h2>Overall Stock Information</h2>
        <table class="table table-striped stock-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Total Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $overall_stock->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['category_name'] . '</td>';
                    echo '<td>' . $row['total_stock'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="category-stock">
            <h2>Stock Information by Category</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Total Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $category_stock->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['category_name'] . '</td>';
                        echo '<td>' . $row['total_stock'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
