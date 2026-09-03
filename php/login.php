<?php
  session_start();
  require '../database/config.php';

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
      $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Enter a valid email address.";
    }

    if ($password === '') {
      $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
      $pdo = getConnection();
      $stmt = $pdo->prepare("SELECT id, name, password FROM accounts WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if (!$user) {
        // No account exists with this email at all
        $errors['email'] = 'You don\'t have an account yet. <a href="signup.php">Sign up here</a>.';
      } elseif (!password_verify($password, $user['password'])) {
        // Account exists, but password is wrong
        $errors['password'] = "Incorrect password.";
      } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
      }
    }
  }

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="auth-split">
  <div class="auth-visual">
    <img src="../images/01_model_side.png" alt="EMPTY HOOD model">
    <div class="auth-visual-overlay">
      <p class="eyebrow">WELCOME BACK</p>
      <h2>PICK UP RIGHT<br>WHERE YOU LEFT OFF.</h2>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <h1>Log In</h1>
      <p class="auth-sub">Enter your details to access your account.</p>

      <form method="post" action="login.php" class="auth-form" novalidate>
        <label>
          Email
          <input type="email" name="email"
                 class="<?php echo isset($errors['email']) ? 'has-error' : ''; ?>"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                 placeholder="you@example.com">
          <?php if (isset($errors['email'])): ?>
            <span class="field-error"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
        </label>

        <label>
          Password
          <input type="password" name="password"
                 class="<?php echo isset($errors['password']) ? 'has-error' : ''; ?>"
                 placeholder="Your password">
          <?php if (isset($errors['password'])): ?>
            <span class="field-error"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
        </label>

        <button type="submit" class="btn-primary btn-full">LOG IN →</button>
      </form>

      <p class="auth-switch">Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>