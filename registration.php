<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/pages/login.css">
    <link rel="stylesheet" href="styles/shared/kits-header.css">
    <title>Register</title>
</head>
<body>
    <header class="kits-header">
        <section class="left-section">
            <a href="index.php" class="header-link">
                <img class="kits-logo" src="images/kits-logo-white.png" alt="Kits Alb Logo">
                <img class="kits-mobile-logo" src="images/kits-mobile-logo-white.png" alt="Kits Alb Mobile Logo">
            </a>
        </section>
    </header>

    <main class="main-content">
        <div class="registration-container">
            <h1>Create an Account</h1>
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p class="success-msg"><?php echo $success; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">

                <button type="submit">Register</button>
            </form>
            <div class="links">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Kits Alb. All rights reserved.</p>
    </footer>
</body>
</html>
