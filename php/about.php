<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="about-page">
  <div class="about-hero">
    <p class="eyebrow">OUR STORY</p>
    <h1>BUILT DIFFERENT.<br>WORN EVERYWHERE.</h1>
  </div>

  <div class="about-content">
    <div class="about-text">
      <p>empty-hood is more than just a brand. It's a mindset. A reminder that even when you feel empty inside, you're still built different.</p>
      <p>We create pieces for those who move in silence, but have a legacy. Every drop is designed with intention — clean lines, subtle branding, and quality that speaks for itself before the logo ever does.</p>
      <p>What started as a small idea has grown into a community of people who don't need to shout to be seen. That's the whole point: built different, worn everywhere.</p>
    </div>

    <div class="about-images">
      <img src="../images/01_model_side.png" alt="Model wearing EH hoodie">
      <img src="../images/02_back_hoodie.png" alt="EH hoodie back design">
      <img src="../images/03_model_stairs.png" alt="Model on stairs">
    </div>
  </div>

  <div class="about-cta">
    <h2>READY TO WEAR IT?</h2>
    <a href="shop.php" class="btn-primary">SHOP THE COLLECTION →</a>
  </div>
</section>

<?php include 'footer.php'; ?>