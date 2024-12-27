<?php
$form_submitted = isset($_POST['submit']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link rel="stylesheet" href="styles/pages/contact.css">
  <link rel="stylesheet" href="styles/shared/kits-header.css">
  <link rel="stylesheet" href="styles/shared/kits-footer.css">
  <style>

    body {
      font-family: Roboto, Arial;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }


    .kits-footer {
      background-color: #222;
      color: white;
      text-align: center;
      padding: 20px 10px;
      position: relative;
      bottom: 0;
      width: 100%;
      margin-top: auto;
    }

    .kits-footer a {
      color: #FFD700;
      text-decoration: none;
    }

    .popup-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .popup-content {
      background: linear-gradient(135deg,rgb(237, 255, 255),rgb(237, 255, 255));
      padding: 30px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      width: 90%;
      max-width: 500px;
    }

    .popup-header {
      font-size: 1.8rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
    }

    .popup-message {
      font-size: 1.2rem;
      color: #555;
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .popup-divider {
      height: 2px;
      width: 80%;
      background: #FFD700;
      margin: 20px auto;
      border-radius: 2px;
    }

    .button-container {
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .popup-button {
      padding: 10px 20px;
      font-size: 1rem;
      font-weight: bold;
      text-transform: uppercase;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      text-decoration: none;
      color: white;
      flex: 1;
      max-width: 150px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .popup-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .shop-button {
      background: linear-gradient(0deg,#febd69,rgb(247, 202, 0));
    }

    .return-button {
      background-color: rgb(19, 25, 33);
    }
  </style>
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

<div class="new_home_web">
  <div class="responsive-container-block big-container">
    <?php if (!$form_submitted): ?>
      <div class="responsive-container-block textContainer">
        <div class="topHead">
          <p class="text-blk heading">Get in <span class="yellowText">touch</span></p>
          <div class="yellowLine" id="w-c-s-bgc_p-2-dm-id"></div>
        </div>
        <p class="text-blk subHeading">
          Have questions or feedback? We’d love to hear from you!
        </p>
      </div>
      <div class="responsive-container-block container">
        <div class="responsive-cell-block wk-tab-12 wk-mobile-12 wk-desk-7 wk-ipadp-10 line" id="i69b">
          <form method="POST" class="form-box">
            <div class="container-block form-wrapper">
              <div class="responsive-container-block">
                <div class="left4">
                  <div class="responsive-cell-block wk-ipadp-6 wk-tab-12 wk-mobile-12 wk-desk-6">
                    <input class="input" name="FirstName" placeholder="First Name" required>
                  </div>
                  <div class="responsive-cell-block wk-desk-6 wk-ipadp-6 wk-tab-12 wk-mobile-12">
                    <input class="input" name="LastName" placeholder="Last Name" required>
                  </div>
                  <div class="responsive-cell-block wk-desk-6 wk-ipadp-6 wk-tab-12 wk-mobile-12">
                    <input class="input" name="Email" type="email" placeholder="Email Address" required>
                  </div>
                  <div class="responsive-cell-block wk-desk-6 wk-ipadp-6 wk-tab-12 wk-mobile-12 lastPhone">
                    <input class="input" name="PhoneNumber" placeholder="Phone Number">
                  </div>
                </div>
                <div class="responsive-cell-block wk-tab-12 wk-mobile-12 wk-desk-12 wk-ipadp-12">
                  <textarea class="textinput" name="Message" placeholder="Message" required></textarea>
                </div>
              </div>
              <input type="submit" name="submit" class="send">
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if ($form_submitted): ?>

  <div class="popup-modal" style="display: flex;">
    <div class="popup-content">
      <div class="popup-header">Thank You!</div>
      <p class="popup-message">
        Your message has been submitted. Please check your email for our response!
      </p>
      <div class="popup-divider"></div>
      <div class="button-container">

        <a href="catalog.php" class="popup-button shop-button">Shop Now</a>

        <a href="contact.php" class="popup-button return-button">Return</a>
      </div>
    </div>
  </div>
<?php endif; ?>

<footer class="kits-footer">
  <p>&copy; 2024 Football Kits Albania <br> Follow us on 
    <a href="https://instagram.com/kits.alb" target="_blank" class="footer-link">Instagram</a>
  </p>
</footer>

</body>
</html>
