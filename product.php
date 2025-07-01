<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection
include 'connection.php';

// Initialize variables for success and error messages
$success = '';
$error = '';

// If the form is submitted, save product data to the database
if (isset($_POST['product_name'])) {
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $year = intval($_POST['year']);

    // Prepare the SQL statement to prevent SQL injection
    if ($stmt = $conn->prepare("INSERT INTO products (name, price, year) VALUES (?, ?, ?)")) {
        $stmt->bind_param("sdi", $product_name, $price, $year);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product: " . $conn->error;
        }
        // Close the statement
        $stmt->close();
    } else {
        $error = "Database query error: " . $conn->error;
    }
}

// If an edit request is made, update the product
if (isset($_POST['edit_product_id'])) {
    $product_id = intval($_POST['edit_product_id']);
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $year = intval($_POST['year']);

    // Prepare the SQL statement to update the product
    if ($stmt = $conn->prepare("UPDATE products SET name=?, price=?, year=? WHERE id=?")) {
        $stmt->bind_param("sdii", $product_name, $price, $year, $product_id);

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
        } else {
            $error = "Error updating product: " . $conn->error;
        }
        // Close the statement
        $stmt->close();
    } else {
        $error = "Database query error: " . $conn->error;
    }
}

// If a delete request is made, delete the product
if (isset($_POST['delete_product_id'])) {
    $product_id = intval($_POST['delete_product_id']);

    // Prepare the SQL statement to delete the product
    if ($stmt = $conn->prepare("DELETE FROM products WHERE id=?")) {
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            $success = "Product deleted successfully!";
        } else {
            $error = "Error deleting product: " . $conn->error;
        }
        // Close the statement
        $stmt->close();
    } else {
        $error = "Database query error: " . $conn->error;
    }
}

// Fetch all products from the database to display
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS file -->
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->

    <div class="content">
        <h1>Add New Product</h1>
        <div class="container">
            <div class="form-container">
                <form action="product.php" method="POST">
                    <div class="input-box">
                        <input type="text" name="product_name" required>
                        <label>Product Name</label>
                    </div>
                    <div class="input-box">
                        <input type="number" name="price" step="0.01" required>
                        <label>Price (INR)</label>
                    </div>
                    <div class="input-box">
                        <input type="number" name="year" required>
                        <label>Year</label>
                    </div>
                    <input type="submit" class="submit-btn" value="Add Product">
                </form>
                <?php if ($success): ?>
                    <div style="color: green;"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div style="color: red;"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <h2>Product List</h2>
                <div class="table-wrapper">
                    <table class="table" id="products-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price (INR)</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>₹<?php echo htmlspecialchars($product['price']); ?></td> <!-- Updated to INR -->
                                        <td><?php echo htmlspecialchars($product['year']); ?></td>
                                        <td>
                                            <form action="product.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="edit_product_id" value="<?php echo $product['id']; ?>">
                                                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                                <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                                <input type="number" name="year" value="<?php echo htmlspecialchars($product['year']); ?>" required>
                                                <input type="submit" class="edit-btn" value="Update">
                                            </form>
                                            <form action="product.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="delete_product_id" value="<?php echo $product['id']; ?>">
                                                <input type="submit" class="delete-btn" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No products added yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
