<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Privacy Policy</h1>
  <p>We collect only the information necessary to process your orders: name, email, address, and phone number.</p>
  <h2>How We Use Your Data</h2>
  <p>Your information is used solely for order fulfillment, account management, and — if you opt in — newsletter updates.</p>
  <h2>Data Security</h2>
  <p>Passwords are securely hashed and never stored in plain text. We do not sell or share your personal data with third parties.</p>
</section>
<?php include 'footer.php'; ?>