<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Shipping & Delivery</h1>
  <p>We ship nationwide via our trusted courier partners.</p>
  <h2>Delivery Times</h2>
  <p>Metro Manila: 1-3 business days. Provincial: 3-7 business days.</p>
  <h2>Shipping Fees</h2>
  <p>A flat rate of ₱120 applies to all orders, regardless of size.</p>
  <h2>Order Tracking</h2>
  <p>Once your order ships, you can check its status anytime from your <a href="order.php">Order History</a> page.</p>
</section>
<?php include 'footer.php'; ?>