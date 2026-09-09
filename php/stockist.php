<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Stockists</h1>
  <p>EMPTY HOOD is currently available exclusively online through this store.</p>
  <p>Interested in carrying EH in your shop? <a href="contact.php">Get in touch</a>.</p>
</section>
<?php include 'footer.php'; ?>