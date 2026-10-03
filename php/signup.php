<?php require_once __DIR__ . '/error_handler.php'; ?>
<?php
session_start();
require_once '../database/config.php';
require_once 'validation.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = validateSignupInput($_POST);
  $errors = $result['errors'];
  $data = $result['data'];

  if (empty($errors)) {
    $pdo = getConnection();

    $check = $pdo->prepare("SELECT id FROM accounts WHERE email = ?");
    $check->execute([$data['email']]);

    if ($check->fetch()) {
      $errors['email'] = "An account with that email already exists.";
    } else {
      $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

      $insert = $pdo->prepare("INSERT INTO accounts (name, email, password) VALUES (?, ?, ?)");
      $insert->execute([$data['name'], $data['email'], $hashedPassword]);

      $_SESSION['user_id'] = $pdo->lastInsertId();
      $_SESSION['user_name'] = $data['name'];

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

      <form method="post" action="signup.php" class="auth-form" novalidate autocomplete="off">
        <label>
          Full Name
          <input type="text" name="name" autocomplete="off"
            class="<?php echo isset($errors['name']) ? 'has-error' : ''; ?>"
            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="Jane Doe">
          <?php if (isset($errors['name'])): ?>
            <span class="field-error"><?php echo $errors['name']; ?></span>
          <?php endif; ?>
        </label>
        <label>
          Email
          <input type="email" name="email" autocomplete="off"
            class="<?php echo isset($errors['email']) ? 'has-error' : ''; ?>"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="you@example.com">
          <?php if (isset($errors['email'])): ?>
            <span class="field-error"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
        </label>
        <label>
          Password
          <div class="password-wrapper">
            <input type="password" name="password" id="password" autocomplete="new-password"
              class="<?php echo isset($errors['password']) ? 'has-error' : ''; ?>" placeholder="At least 6 characters">
            <button type="button" class="toggle-password" data-target="password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
                <?php if (isset($errors['password'])): ?>
            <span class="field-error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
        </label>
        <label>
          Confirm Password
          <div class="password-wrapper">
            <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password"
              class="<?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>"
              placeholder="Re-enter your password">
            <button type="button" class="toggle-password" data-target="confirm_password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
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