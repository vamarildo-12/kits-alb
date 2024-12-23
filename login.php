<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/pages/login.css">
    <title>Login</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="images/kits-logo-white.png" alt="Kits.alb Logo">
        </a>
    </header>
    <main>
        <div class="login-container">
            <h1>Sign-In</h1>
            <form action="process_login.php" method="POST">
                <label for="email">Email or mobile phone number</label>
                <input type="text" id="email" name="email" required placeholder="Enter your email or phone number" oninput="validateEmailOrPhone()">
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
                <p class="error-msg"><?php echo $error ?? ''; ?></p>
            </form>
            <div class="links">
                <a href="passwordreset.php">Forgot your password?</a>
                <p>New to Kits.alb? <a href="registration.php">Create your Kits.alb account</a></p>
            </div>
        </div>
    </main>
    <script>
        function validateEmailOrPhone() {
            var emailInput = document.getElementById("email").value;
            if (emailInput.includes('@')) {
                document.getElementById("email").setAttribute("type", "email");
            } else {
                document.getElementById("email").setAttribute("type", "text");
            }
        }

        function togglePassword() {
            var password = document.getElementById("password");
            password.type = password.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
