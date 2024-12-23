<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/pages/login.css">
    <title>Create Account</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="images/kits-logo-white.png" alt="Kits.alb Logo">
        </a>
    </header>
    <main>
        <div class="registration-container">
            <h1>Create account</h1>
            <form action="process_registration.php" method="POST">
                <label for="name">Your name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your name">

                <label for="email">Email or mobile phone number</label>
                <input type="text" id="email" name="email" required placeholder="Enter your email or phone number" oninput="validateEmailOrPhone()">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <label for="confirm_password">Re-enter password</label>
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

        function validateEmailOrPhone() {
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
