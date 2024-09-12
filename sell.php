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

$message = '';

// Handle form submission for generating a bill
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_address = $_POST['customer_address'];
    $customer_mobile = $_POST['customer_mobile'];
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;

    // Insert into sales table
    $sql = "INSERT INTO sales (customer_name, customer_address, customer_mobile, total_amount) VALUES ('$customer_name', '$customer_address', '$customer_mobile', 0)";
    $conn->query($sql);
    $sale_id = $conn->insert_id;

    // Insert into sale_items and update stock
    for ($i = 0; $i < count($products); $i++) {
        $product_id = $products[$i];
        $quantity = $quantities[$i];
        
        $sql = "SELECT price, stock FROM products WHERE id = $product_id";
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();
        
        if ($product['stock'] < $quantity) {
            $message = "Insufficient stock for product ID $product_id.";
            break;
        }

        $price = $product['price'];
        $total_amount += $price * $quantity;

        // Insert sale item
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES ($sale_id, $product_id, $quantity, $price)";
        $conn->query($sql);

        // Update stock
        $sql = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
        $conn->query($sql);
    }

    // Update total amount in sales table
    $sql = "UPDATE sales SET total_amount = $total_amount WHERE id = $sale_id";
    $conn->query($sql);

    $message = "Bill generated successfully! Total Amount: $" . number_format($total_amount, 2);
}

// Fetch products for the dropdown
$sql = "SELECT * FROM products WHERE stock > 0";
$products = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - Akash Communication</title>
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
        .form-section {
            margin-top: 30px;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
    </style>
    <script>
        function addProductRow() {
            let row = document.getElementById('product-row-template').cloneNode(true);
            row.style.display = 'block';
            document.getElementById('products-container').appendChild(row);
        }
    </script>
</head>
<body>
    <?php
        require 'nav.php';
    ?>

    <div class="container mt-5">
        <h2>Generate Bill</h2>
        <form method="POST" action="./bill.php">
            <div class="form-section">
                <h5>Customer Information</h5>
                <div class="mb-3">
                    <input type="text" name="customer_name" class="form-control" placeholder="Customer Name" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="customer_address" class="form-control" placeholder="Customer Address" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="customer_mobile" class="form-control" placeholder="Customer Mobile" required>
                </div>
            </div>

            <div class="form-section">
                <h5>Products</h5>
                <div id="products-container">
                    <div class="row mb-3" id="product-row-template" >
                        <div class="col-md-6">
                            <select name="product[]" class="form-select" required>
                                <option value="">Select Product</option>
                                <?php
                                while ($product = $products->fetch_assoc()) {
                                    echo '<option value="' . $product['id'] . '">' . $product['name'] . ' (₹' . $product['price'] . ' per unit, ' . $product['stock'] . ' in stock)</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" min="1" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addProductRow()">Add Another Product</button>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Generate Bill</button>
        </form>

        <?php if ($message) : ?>
        <div class="message">
            <strong><?php echo $message; ?></strong>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
