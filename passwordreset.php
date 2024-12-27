<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $success = "Password reset successful. You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } else {
            $error = "No account found with this email.";
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
    <title>Reset Password</title>
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
        <div class="reset-container">
            <h1>Reset Your Password</h1>
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p class="success-msg"><?php echo $success; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">

                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required placeholder="Enter a new password">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">

                <button type="submit">Reset Password</button>
            </form>
            <div class="links">
                <p>Back to <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Kits Alb. All rights reserved.</p>
    </footer>
</body>
</html>
