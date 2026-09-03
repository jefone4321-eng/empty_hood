<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="lookbook-page">
  <div class="section-header">
    <h1>LOOKBOOK</h1>
    <a href="https://instagram.com/empty.hood" class="follow-us" target="_blank">
      FOLLOW US <i class="fa-brands fa-instagram"></i> @empty.hood →
    </a>
  </div>

  <div class="lookbook-grid">
    <img src="../images/04_back_tshirt.png" alt="Lookbook photo 1">
    <img src="../images/05_model_camera.png" alt="Lookbook photo 2">
    <img src="../images/06_back_hoodie_street.png" alt="Lookbook photo 3">
    <img src="../images/07_back_hoodie_close.png" alt="Lookbook photo 4">
    <img src="../images/08_label_patch.png" alt="Lookbook photo 5">
    <img src="../images/09_full_model_street.png" alt="Lookbook photo 6">
    <img src="../images/01_model_side.png" alt="Lookbook photo 7">
    <img src="../images/02_back_hoodie.png" alt="Lookbook photo 8">
    <img src="../images/03_model_stairs.png" alt="Lookbook photo 9">
  </div>
</section>

<?php include 'footer.php'; ?>