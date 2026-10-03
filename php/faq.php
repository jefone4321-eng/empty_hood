<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';

  $faqs = [
    ["q" => "How long does shipping take?", "a" => "1-3 business days within Metro Manila, 3-7 days for provincial orders."],
    ["q" => "Can I cancel my order?", "a" => "Orders can be cancelled before they're marked as Shipped. Contact us right away if you need to cancel."],
    ["q" => "Do you accept returns?", "a" => "Yes, within 7 days of delivery, provided items are unworn with tags attached."],
    ["q" => "What payment methods do you accept?", "a" => "Cash on Delivery and GCash."],
  ];
?>
<section class="info-page">
  <h1>Frequently Asked Questions</h1>
  <?php foreach ($faqs as $faq): ?>
    <div class="faq-item">
      <h2><?php echo htmlspecialchars($faq['q']); ?></h2>
      <p><?php echo htmlspecialchars($faq['a']); ?></p>
    </div>
  <?php endforeach; ?>
</section>
<?php include 'footer.php'; ?>