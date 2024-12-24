<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/pages/login.css">
    <title>Reset Password</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="images/kits-logo-white.png" alt="Kits.alb Logo">
        </a>
    </header>
    
        <div class="password-reset-container">
            <h1>Reset Password</h1>
            <form action="process_passwordreset.php" method="POST">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required placeholder="Enter your email" oninput="validateEmail()">
                <button type="submit">Continue</button>
                <p class="confirmation-msg"><?php echo $confirmation ?? ''; ?></p>
            </form>
            <div class="links">
                <a href="login.php">Back to Sign-In</a>
            </div>
        </div>
    </main>
    <script>
        function validateEmail() {
            var emailInput = document.getElementById("email").value;
            if (emailInput.includes('@')) {
                document.getElementById("email").setAttribute("type", "email");
            } else {
                document.getElementById("email").setAttribute("type", "text");
            }
        }
    </script>
</body>
</html>
