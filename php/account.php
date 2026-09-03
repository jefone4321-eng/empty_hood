<?php
  session_start();
  require '../database/config.php';

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $pdo = getConnection();
  $errors = [];
  $success = null;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    // --- Handle profile info update ---
    if ($formAction === 'update_profile') {
      $name = trim($_POST['name'] ?? '');
      $phone = trim($_POST['phone'] ?? '');
      $address = trim($_POST['address'] ?? '');

      if ($name === '') {
        $errors['name'] = "Name cannot be empty.";
      } else {
        $update = $pdo->prepare("UPDATE accounts SET name = ?, phone = ?, address = ? WHERE id = ?");
        $update->execute([$name, $phone, $address, $_SESSION['user_id']]);
        $_SESSION['user_name'] = $name;
        $success = 'profile';
      }
    }

    // --- Handle profile picture upload ---
    if ($formAction === 'upload_picture' && isset($_FILES['profile_picture'])) {
      $file = $_FILES['profile_picture'];
      $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      $maxSize = 2 * 1024 * 1024; // 2MB

      if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors['picture'] = "Upload failed. Please try again.";
      } elseif (!in_array($file['type'], $allowedTypes)) {
        $errors['picture'] = "Only JPG, PNG, or WEBP images are allowed.";
      } elseif ($file['size'] > $maxSize) {
        $errors['picture'] = "Image must be smaller than 2MB.";
      } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = "user_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
        $destination = "../images/profile_pictures/" . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
          $update = $pdo->prepare("UPDATE accounts SET profile_picture = ? WHERE id = ?");
          $update->execute([$newFilename, $_SESSION['user_id']]);
          $success = 'picture';
        } else {
          $errors['picture'] = "Could not save the uploaded file.";
        }
      }
    }

    // --- Handle password change ---
    if ($formAction === 'change_password') {
      $currentPassword = $_POST['current_password'] ?? '';
      $newPassword = $_POST['new_password'] ?? '';
      $confirmPassword = $_POST['confirm_new_password'] ?? '';

      $stmt = $pdo->prepare("SELECT password FROM accounts WHERE id = ?");
      $stmt->execute([$_SESSION['user_id']]);
      $row = $stmt->fetch();

      if (!password_verify($currentPassword, $row['password'])) {
        $errors['current_password'] = "Current password is incorrect.";
      } elseif (strlen($newPassword) < 6) {
        $errors['new_password'] = "New password must be at least 6 characters.";
      } elseif ($newPassword !== $confirmPassword) {
        $errors['confirm_new_password'] = "Passwords do not match.";
      } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE accounts SET password = ? WHERE id = ?");
        $update->execute([$hashed, $_SESSION['user_id']]);
        $success = 'password';
      }
    }
  }

  // Fetch current account details (fresh, after any updates above)
  $stmt = $pdo->prepare("SELECT name, email, phone, address, profile_picture, created_at FROM accounts WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $account = $stmt->fetch();

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="account-page">
  <div class="account-header">
    <div class="account-avatar">
      <?php if ($account['profile_picture']): ?>
        <img src="../images/profile_pictures/<?php echo htmlspecialchars($account['profile_picture']); ?>" alt="Profile picture">
      <?php else: ?>
        <i class="fa-solid fa-user"></i>
      <?php endif; ?>
    </div>
    <div>
      <h1><?php echo htmlspecialchars($account['name']); ?></h1>
      <p class="account-email"><?php echo htmlspecialchars($account['email']); ?></p>
      <p class="account-since">Member since <?php echo date("F Y", strtotime($account['created_at'])); ?></p>
    </div>
  </div>

  <!-- Profile Picture -->
  <div class="account-section">
    <h2>Profile Picture</h2>
    <?php if ($success === 'picture'): ?>
      <p class="form-success">Profile picture updated.</p>
    <?php endif; ?>
    <?php if (isset($errors['picture'])): ?>
      <p class="field-error"><?php echo $errors['picture']; ?></p>
    <?php endif; ?>
    <form method="post" action="account.php" enctype="multipart/form-data" class="auth-form">
      <input type="hidden" name="form_action" value="upload_picture">
      <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp">
      <button type="submit" class="btn-primary">UPLOAD →</button>
    </form>
  </div>

  <!-- Profile Info -->
  <div class="account-section">
    <h2>Edit Profile</h2>
    <?php if ($success === 'profile'): ?>
      <p class="form-success">Your profile has been updated.</p>
    <?php endif; ?>
    <form method="post" action="account.php" class="auth-form" novalidate>
      <input type="hidden" name="form_action" value="update_profile">
      <label>
        Full Name
        <input type="text" name="name"
               class="<?php echo isset($errors['name']) ? 'has-error' : ''; ?>"
               value="<?php echo htmlspecialchars($account['name']); ?>">
        <?php if (isset($errors['name'])): ?>
          <span class="field-error"><?php echo $errors['name']; ?></span>
        <?php endif; ?>
      </label>
      <label>
        Phone Number
        <input type="text" name="phone" value="<?php echo htmlspecialchars($account['phone'] ?? ''); ?>" placeholder="09XX XXX XXXX">
      </label>
      <label>
        Shipping Address
        <textarea name="address" rows="3" placeholder="Street, city, province"><?php echo htmlspecialchars($account['address'] ?? ''); ?></textarea>
      </label>
      <button type="submit" class="btn-primary">SAVE CHANGES →</button>
    </form>
  </div>

  <!-- Change Password -->
  <div class="account-section">
    <h2>Change Password</h2>
    <?php if ($success === 'password'): ?>
      <p class="form-success">Password changed successfully.</p>
    <?php endif; ?>
    <form method="post" action="account.php" class="auth-form" novalidate>
      <input type="hidden" name="form_action" value="change_password">
      <label>
        Current Password
        <input type="password" name="current_password"
               class="<?php echo isset($errors['current_password']) ? 'has-error' : ''; ?>">
        <?php if (isset($errors['current_password'])): ?>
          <span class="field-error"><?php echo $errors['current_password']; ?></span>
        <?php endif; ?>
      </label>
      <label>
        New Password
        <input type="password" name="new_password"
               class="<?php echo isset($errors['new_password']) ? 'has-error' : ''; ?>">
        <?php if (isset($errors['new_password'])): ?>
          <span class="field-error"><?php echo $errors['new_password']; ?></span>
        <?php endif; ?>
      </label>
      <label>
        Confirm New Password
        <input type="password" name="confirm_new_password"
               class="<?php echo isset($errors['confirm_new_password']) ? 'has-error' : ''; ?>">
        <?php if (isset($errors['confirm_new_password'])): ?>
          <span class="field-error"><?php echo $errors['confirm_new_password']; ?></span>
        <?php endif; ?>
      </label>
      <button type="submit" class="btn-primary">CHANGE PASSWORD →</button>
    </form>
  </div>

  <div class="account-section">
    <h2>Account</h2>
    <a href="logout.php" class="logout-link">Log Out</a>
  </div>
</section>

<?php include 'footer.php'; ?>