<?php
session_start();
require_once '../database/config.php';
require_once 'validation.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = validateLoginInput($_POST);
  $errors = $result['errors'];
  $data = $result['data'];

  if (empty($errors)) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, name, password, is_admin FROM accounts WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user) {
      $errors['email'] = " You don't have an account yet. <a href='signup.php'>Sign up</a>.";
    } elseif (!password_verify($data['password'], $user['password'])) {
      $errors['password'] = "Incorrect password.";
    } else {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

      if (!empty($_POST['remember_me'])) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        $update = $pdo->prepare("UPDATE accounts SET remember_token = ?, remember_expires = ? WHERE id = ?");
        $update->execute([$hashedToken, $expires, $user['id']]);

        setcookie('remember_me', $user['id'] . ':' . $token, time() + (30 * 24 * 60 * 60), '/');
      }

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
          <input type="email" name="email" class="<?php echo isset($errors['email']) ? 'has-error' : ''; ?>"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="you@example.com">
          <?php if (isset($errors['email'])): ?>
            <span class="field-error"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
        </label>

        <label>
          Password
          <div class="password-wrapper">
            <input type="password" name="password" id="password"
              class="<?php echo isset($errors['password']) ? 'has-error' : ''; ?>" placeholder="Your password">
            <button type="button" class="toggle-password" data-target="password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <?php if (isset($errors['password'])): ?>
            <span class="field-error"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
        </label>
        <label class="remember-me-label">
          <input type="checkbox" name="remember_me" value="1"> Remember me
        </label>
        <button type="submit" class="btn-primary btn-full">LOG IN →</button>
      </form>

      <p class="auth-switch">Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>