<?php
include 'connection.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dealer_id = $_POST['dealer_id'];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    $gst_included = isset($_POST['gst']) ? 1 : 0;
    $payment_status = $_POST['payment-status'];

    // Calculate subtotal and total
    $subtotal = 0;
    for ($i = 0; $i < count($products); $i++) {
        $product_id = $products[$i];
        $quantity = $quantities[$i];

        // Get product price from the database
        $result = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
        $product = mysqli_fetch_assoc($result);
        $price = $product['price'];

        $total_price = $price * $quantity;
        $subtotal += $total_price;
    }

    $total = $gst_included ? ($subtotal * 1.18) : $subtotal;

    // Insert into invoices table
    $stmt = $conn->prepare("INSERT INTO invoices (dealer_id, subtotal, total, gst_included, payment_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iddss", $dealer_id, $subtotal, $total, $gst_included, $payment_status);
    $stmt->execute();
    $invoice_id = $stmt->insert_id;

    // Insert into invoice_items table
    for ($i = 0; $i < count($products); $i++) {
        $product_id = $products[$i];
        $quantity = $quantities[$i];

        $result = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
        $product = mysqli_fetch_assoc($result);
        $price = $product['price'];

        $total_price = $price * $quantity;

        $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddd", $invoice_id, $product_id, $quantity, $price, $total_price);
        $stmt->execute();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to the form with a success status
    header("Location: billing.php?status=success"); // Change 'index.php' to your form page
    exit();
}
?>
