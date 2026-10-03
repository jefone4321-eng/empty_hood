<?php
session_start();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$navStyle = "";
include 'header.php';
?>

<section class="lookbook-page">
  <div class="section-header">
    <h1>LOOKBOOK</h1>
    <a href="#" class="follow-us" target="_blank">
      FOLLOW US <i class="fa-brands fa-instagram"></i> @empty.hood →
    </a>
  </div>

  <div class="lookbook-grid">
    <img src="../images/04_back_tshirt.png" alt="Lookbook photo 1" class="lookbook-photo">
    <img src="../images/05_model_camera.png" alt="Lookbook photo 2" class="lookbook-photo">
    <img src="../images/06_back_hoodie_street.png" alt="Lookbook photo 3" class="lookbook-photo">
    <img src="../images/07_back_hoodie_close.png" alt="Lookbook photo 4" class="lookbook-photo">
    <img src="../images/08_label_patch.png" alt="Lookbook photo 5" class="lookbook-photo">
    <img src="../images/09_full_model_street.png" alt="Lookbook photo 6" class="lookbook-photo">
    <img src="../images/01_model_side.png" alt="Lookbook photo 7" class="lookbook-photo">
    <img src="../images/02_back_hoodie.png" alt="Lookbook photo 8" class="lookbook-photo">
    <img src="../images/03_model_stairs.png" alt="Lookbook photo 9" class="lookbook-photo">
  </div>
  <div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose" aria-label="Close">&times;</button>
    <img src="" alt="" id="lightboxImage">
  </div>
</section>

<?php include 'footer.php'; ?>