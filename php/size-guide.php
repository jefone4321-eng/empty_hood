<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>
<section class="info-page">
  <h1>Size Guide</h1>
  <table class="admin-table" style="max-width:500px;">
    <tr><th>Size</th><th>Chest (in)</th><th>Length (in)</th></tr>
    <tr><td>S</td><td>36-38</td><td>27</td></tr>
    <tr><td>M</td><td>39-41</td><td>28</td></tr>
    <tr><td>L</td><td>42-44</td><td>29</td></tr>
    <tr><td>XL</td><td>45-47</td><td>30</td></tr>
  </table>
  <p style="margin-top:20px;">Still unsure? <a href="contact.php">Reach out</a> and we'll help you find your fit.</p>
</section>
<?php include 'footer.php'; ?>