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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_address = $_POST['customer_address'];
    $customer_mobile = $_POST['customer_mobile'];
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;
    
    $items = [];

    // Insert into sales table
    $sql = "INSERT INTO sales (customer_name, customer_address, customer_mobile, total_amount) VALUES ('$customer_name', '$customer_address', '$customer_mobile', 0)";
    $conn->query($sql);
    $sale_id = $conn->insert_id;

    // Insert into sale_items and update stock
    for ($i = 0; $i < count($products); $i++) {
        $product_id = $products[$i];
        $quantity = $quantities[$i];
        
        $sql = "SELECT name, price, stock FROM products WHERE id = $product_id";
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();

        if ($product['stock'] < $quantity) {
            header("refresh:3;url=index.php");
            die("Insufficient stock for product: " . $product['name']);
        }

        $price = $product['price'];
        $total_amount += $price * $quantity;


        $cgst_rate = 0.09;
        $sgst_rate = 0.09;
        $cgst = $total_amount * $cgst_rate / (1 + $cgst_rate + $sgst_rate);
        $sgst = $total_amount * $sgst_rate / (1 + $cgst_rate + $sgst_rate);


        // Save item details for bill display
        $items[] = [
            'name' => $product['name'],
            'quantity' => $quantity,
            'price' => $price,
            'total' => $price * $quantity,
        ];

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
} else {
    die("Invalid request");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill - Akash Communication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            margin-top: 50px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 16px;
        }
        .bill-details {
            margin-bottom: 40px;
        }
        .bill-details p {
            margin: 0;
            font-size: 18px;
        }
        .bill-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .bill-table th, .bill-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        .bill-table th {
            background-color: #f1f1f1;
        }
        .total-amount {
            text-align: right;
            font-weight: bold;
            font-size: 20px;
            margin-top: 20px;
        }
        .thank-you {
            text-align: center;
            margin-top: 40px;
            font-size: 18px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Akash Communication</h1>
        <p>LIC Building, Near Amla Toli, Daltonganj, Palamu, Jharkhand, 822101</p>
        <p>GST No: 27XXXXXXXXXXZ5A</p>
        <p>Phone: +91-9304424639</p>
    </div>

    <div class="bill-details">
        <p><strong>Customer Name:</strong> <?php echo $customer_name; ?></p>
        <p><strong>Address:</strong> <?php echo $customer_address; ?></p>
        <p><strong>Mobile Number:</strong> <?php echo $customer_mobile; ?></p>
        <p><strong>Date:</strong> <?php echo date('d-m-Y H:i:s'); ?></p>
    </div>

    <table class="bill-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (per unit)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $index => $item): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo number_format($item['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-amount">
        Total Amount: ₹<?php echo number_format($total_amount, 2); ?>
        <div class="total-amount">
        CGST (9%): ₹<?php echo number_format($cgst, 2); ?><br>
        SGST (9%): ₹<?php echo number_format($sgst, 2); ?><br>
        <strong>Total Amount (Incl. GST): ₹<?php echo number_format($total_amount, 2); ?></strong>
    </div>
    <div class="thank-you">
        Thank you for your purchase!
    </div>
    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-success">Print Bill</button>
        <a href="index.php" class="btn btn-primary">Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
