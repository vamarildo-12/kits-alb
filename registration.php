<?php

@include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'session_timeout.php');

// Start the session
session_start();

// Database connection (inline)
$servername = "localhost";
$username = "root"; // Update with your MySQL username
$password = ""; // Update with your MySQL password
$dbname = "web"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Error and success messages
$error = '';
$success = '';

// Form submission logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = $_POST['terms'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!$terms) {
        $error = 'You must agree to the terms and conditions.';
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email is already taken.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into the database
            $stmt = $conn->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'There was an error while creating the account. Please try again later.';
            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/pages/login.css">
    <link rel="stylesheet" href="styles/shared/kits-header.css">
    <title>Create Account</title>
</head>
<body>
<header class="kits-header">
        <!-- Left Section: Logo and Branding -->
        <section class="left-section">
            <a href="admin_dashboard.php" class="header-link">
                <img class="kits-logo" src="images/kits-logo-white.png" alt="Kits Alb Logo">
                <img class="kits-mobile-logo" src="images/kits-mobile-logo-white.png" alt="Kits Alb Mobile Logo">
            </a>
        </section>
    </header>
    <main>
        <div class="registration-container">
            <h1>Create Account</h1>
            <!-- Display Error or Success Messages -->
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success-msg"><?php echo $success; ?></p>
            <?php endif; ?>

            <form action="#" method="POST">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your name" value="<?php echo $name ?? ''; ?>">

                <label for="email">Email</label>
                <input type="text" id="email" name="email" required placeholder="Enter your email" value="<?php echo $email ?? ''; ?>" oninput="validateEmail()">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <label for="confirm_password">Re-enter Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">

                <div class="show-password">
                    <input type="checkbox" id="show-password" onclick="togglePassword()"> <label for="show-password">Show Password</label>
                </div>

                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Kits.alb Conditions of Use</a> and <a href="#">Privacy Notice</a>.</label>
                </div>

                <button type="submit">Create your Kits.alb account</button>
            </form>
            <div class="links">
                <p>Already have an account? <a href="login.php">Sign-In</a></p>
            </div>
        </div>
    </main>
    <script>
        function togglePassword() {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirm_password");
            var passwordFieldType = password.type === "password" ? "text" : "password";
            password.type = passwordFieldType;
            confirmPassword.type = passwordFieldType;
        }

        function validateEmail() {
            var emailField = document.getElementById('email');
            var emailValue = emailField.value;

            if (emailValue.includes('@')) {
                emailField.setCustomValidity('');
            } else {
                emailField.setCustomValidity('Please enter a valid email address with "@" symbol.');
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
