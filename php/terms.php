<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Terms & Conditions</h1>
  <p>By using this website and placing an order, you agree to the following terms.</p>
  <h2>Orders</h2>
  <p>All orders are subject to product availability. We reserve the right to cancel any order due to stock issues or pricing errors.</p>
  <h2>Pricing</h2>
  <p>All prices are listed in Philippine Peso (₱) and are subject to change without notice.</p>
  <h2>Account Responsibility</h2>
  <p>You are responsible for maintaining the confidentiality of your account and password.</p>
</section>
<?php include 'footer.php'; ?>