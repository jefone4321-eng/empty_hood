<?php
  session_start();
  require_once __DIR__ . '/../../database/config.php';
  require_once __DIR__ . '/../csrf.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
  http_response_code(403);
  die('Invalid request.');
}

  // If already logged in as admin, skip straight to dashboard
  if (isset($_SESSION['user_id']) && !empty($_SESSION['is_admin'])) {
    header("Location: dashboard.php");
    exit;
  } 



  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
      $errors['email'] = "Email is required.";
    }

    if ($password === '') {
      $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
      $pdo = getConnection();
      $stmt = $pdo->prepare("SELECT id, name, password, is_admin FROM accounts WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if (!$user) {
        $errors['email'] = "No account found with that email.";
      } elseif (!password_verify($password, $user['password'])) {
        $errors['password'] = "Incorrect password.";
      } elseif (!$user['is_admin']) {
        $errors['email'] = "This account does not have admin access.";
      } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: dashboard.php");
        exit;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login — Empty Hood</title>
    <link rel="stylesheet" href="../../css/style-admin.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
</head>
<body class="admin-body">

<div class="admin-login-wrapper">
    <div class="admin-login-box">
        <div class="admin-login-icon"><i class="fa-solid fa-lock"></i></div>
        <h1>EH ADMIN</h1>
        <p class="admin-login-sub">Restricted access — authorized personnel only.</p>

        <form method="post" action="admin_login.php" novalidate>
           <?php echo csrf_field(); ?>
            <div class="admin-form-group">
                <label>
                    Email
                    <input type="email" name="email"
                           class="<?php echo isset($errors['email']) ? 'has-error' : ''; ?>"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="field-error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </label>
                <label>
                    Password
                    <input type="password" name="password"
                           class="<?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                    <?php if (isset($errors['password'])): ?>
                        <span class="field-error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </label>
                <button type="submit" class="admin-login-submit">LOG IN →</button>
            </div>
        </form>

        <p class="admin-login-back"><a href="../index.php">← Back to site</a></p>
    </div>
</div>

</body>
</html>