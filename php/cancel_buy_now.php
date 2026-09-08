<?php
  session_start();

  // Nothing was ever deducted for a Buy Now reservation under the new
  // model, so cancelling is just clearing the session entry.
  unset($_SESSION['buy_now']);

  header("Location: shop.php");
  exit;
?>