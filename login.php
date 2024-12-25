<?php
@include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'session_timeout.php');
// Inline database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "web"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize session
session_start();

// Initialize variables
$error = '';
$email = '';
$password = '';

// Track failed login attempts for guests (session-based)
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
    $_SESSION['last_failed_attempt'] = null;
}

$max_failed_attempts = 7;
$block_duration = 1800; // 30 minutes in seconds

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user has reached the maximum failed attempts
    if ($_SESSION['failed_attempts'] >= $max_failed_attempts) {
        $last_failed_attempt = $_SESSION['last_failed_attempt'];
        $current_time = time();
        $time_diff = $current_time - $last_failed_attempt;

        if ($time_diff < $block_duration) {
            $error = "Too many failed attempts. Please try again in 30 minutes.";
        } else {
            // Reset failed attempts after the block duration has passed
            $_SESSION['failed_attempts'] = 0;
        }
    }

    // If not blocked, continue checking login
    if (empty($error)) {
        // Prepare and execute query to check the user credentials
        $stmt = $conn->prepare("SELECT id, password, role FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email); 
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Successful login, reset failed attempts
                $_SESSION['failed_attempts'] = 0;

                // Start session and redirect based on user role
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                // Invalid password, increment failed attempts
                $_SESSION['failed_attempts'] += 1;
                $_SESSION['last_failed_attempt'] = time();

                $error = "Invalid password. Please try again.";
            }
        } else {
            // No account found with that email
            $_SESSION['failed_attempts'] += 1;
            $_SESSION['last_failed_attempt'] = time();

            $error = "No account found with that email.";
        }

        $stmt->close();
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
    <link rel="stylesheet" href="styles/pages/login.css">
    <link rel="stylesheet" href="styles/shared/kits-header.css">
    <title>Login</title>
</head>
<body>
<header class="kits-header">
        <!-- Left Section: Logo and Branding -->
        <section class="left-section">
            <a href="index.php" class="header-link">
                <img class="kits-logo" src="images/kits-logo-white.png" alt="Kits Alb Logo">
                <img class="kits-mobile-logo" src="images/kits-mobile-logo-white.png" alt="Kits Alb Mobile Logo">
            </a>
        </section>
    </header>
    <main>
        <div class="login-container">
            <h1>Sign-In</h1>
            <!-- Show error message if login failed -->
            <?php if ($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <div class="checkbox-container">
                    <input type="checkbox" id="show-password" onclick="togglePassword()">
                    <label for="show-password">Show Password</label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="keep-signed-in" name="keep-signed-in">
                    <label for="keep-signed-in">Keep me signed in</label>
                </div>

                <button type="submit">Sign-In</button>
            </form>
            <div class="links">
                <a href="passwordreset.php">Forgot your password?</a>
                <p>New to Kits.alb? <a href="registration.php">Create your Kits.alb account</a></p>
            </div>
        </div>
    </main>

    <script>
        function togglePassword() {
            var password = document.getElementById("password");
            password.type = password.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
