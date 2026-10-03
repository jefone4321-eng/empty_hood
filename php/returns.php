<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Returns & Exchanges</h1>
  <p>Not the right fit? We accept returns and exchanges within 7 days of delivery.</p>
  <h2>Eligibility</h2>
  <p>Items must be unworn, unwashed, and with original tags attached.</p>
  <h2>How to Start a Return</h2>
  <p>Contact us at <a href="contact.php">our contact page</a> with your order number, and we'll walk you through the process.</p>
</section>
<?php include 'footer.php'; ?>