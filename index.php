<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost'; // Change if your database host is different
$username = 'root'; // Database username
$password = 'root1234'; // Database password
$dbname = 'bill'; // Database name

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for error messages
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data and sanitize it
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if both username and password are entered
    if (!empty($username) && !empty($password)) {
        // Prepare the SQL statement to prevent SQL injection
        if ($stmt = $conn->prepare("SELECT * FROM users WHERE username = ?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Check if the password matches
                if ($row['password'] === $password) {
                    // If the login is successful, store the username in the session and redirect
                    $_SESSION['username'] = $username;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Invalid password error
                    $error = "Invalid password. Please try again.";
                }
            } else {
                // No user found with the given username
                $error = "No user found with that username.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Prepared statement failed
            $error = "Database query error: " . $conn->error;
        }
    } else {
        $error = "Please enter both username and password.";
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
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Company Logo -->
            <div class="logo">
                <img src="logo.png" alt="Dishank Solutions Logo">
            </div>
            <!-- Company Name -->
            <h1 class="company-name">Dishank Solutions</h1>
            <hr><br><br>

            <!-- Display error message if there is any -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <p style="color:red;"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST"> <!-- Same page form submission -->
                <div class="input-box">
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
