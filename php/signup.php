<?php
  session_start();
  require '../database/config.php';

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
      $errors['name'] = "Name is required.";
    }

    if ($email === '') {
      $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Enter a valid email address.";
    }

    if ($password === '') {
      $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
      $errors['password'] = "Must be at least 6 characters.";
    }

    if ($confirmPassword === '') {
      $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
      $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
      $pdo = getConnection();

      $check = $pdo->prepare("SELECT id FROM accounts WHERE email = ?");
      $check->execute([$email]);

      if ($check->fetch()) {
        $errors['email'] = "An account with that email already exists.";
      } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insert = $pdo->prepare("INSERT INTO accounts (name, email, password) VALUES (?, ?, ?)");
        $insert->execute([$name, $email, $hashedPassword]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;

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
    <img src="../images/03_model_stairs.png" alt="EMPTY HOOD model">
    <div class="auth-visual-overlay">
      <p class="eyebrow">EMPTY HOOD</p>
      <h2>BUILT DIFFERENT.<br>WORN EVERYWHERE.</h2>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <h1>Create Account</h1>
      <p class="auth-sub">Join for faster checkout and early access to drops.</p>

      <form method="post" action="signup.php" class="auth-form" novalidate>
        <label>
          Full Name
          <input type="text" name="name"
                 class="<?php echo isset($errors['name']) ? 'has-error' : ''; ?>"
                 value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                 placeholder="Jane Doe">
          <?php if (isset($errors['name'])): ?>
            <span class="field-error"><?php echo $errors['name']; ?></span>
          <?php endif; ?>
        </label>

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
                 placeholder="At least 6 characters">
          <?php if (isset($errors['password'])): ?>
            <span class="field-error"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
        </label>

        <label>
          Confirm Password
          <input type="password" name="confirm_password"
                 class="<?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>"
                 placeholder="Re-enter your password">
          <?php if (isset($errors['confirm_password'])): ?>
            <span class="field-error"><?php echo $errors['confirm_password']; ?></span>
          <?php endif; ?>
        </label>

        <button type="submit" class="btn-primary btn-full">CREATE ACCOUNT →</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>