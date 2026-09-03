<?php
  session_start();

  $action = $_POST['action'] ?? 'add';
  $id = $_POST['id'] ?? null;

  if ($id) {
    switch ($action) {
      case 'add':
        if (!isset($_SESSION['cart'][$id])) {
          $_SESSION['cart'][$id] = 0;
        }
        $_SESSION['cart'][$id]++;
        break;

      case 'increase':
        $_SESSION['cart'][$id]++;
        break;

      case 'decrease':
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) {
          unset($_SESSION['cart'][$id]);
        }
        break;

      case 'remove':
        unset($_SESSION['cart'][$id]);
        break;
    }
  }

  $redirectTo = $_POST['redirect'] ?? 'index.php';
  header("Location: $redirectTo");
  exit;
?>