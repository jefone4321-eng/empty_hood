<?php
  session_start();
  require_once '../database/config.php';
  require_once 'validation.php';

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';

  $errors = [];
  $success = false;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $result = validateContactInput($_POST);
     $errors = $result['errors'];

     if (empty($errors)) {
        $pdo = getConnection();

        $insert = $pdo->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
        $insert->execute([
          $result['data']['name'],
          $result['data']['email'],
          $result['data']['message'],
        ]);

        $success = true;
     }
  }
?>

<section class="auth-split">
  <div class="auth-visual">
    <img src="../images/02_back_hoodie.png" alt="EMPTY HOOD hoodie detail">
    <div class="auth-visual-overlay">
      <p class="eyebrow">GET IN TOUCH</p>
      <h2>QUESTIONS?<br>WE'RE HERE.</h2>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <h1>Contact Us</h1>
      <p class="auth-sub">Sizing questions, repairs, wholesale — write in and we'll get back to you.</p>

      <?php if ($success): ?>
        <p class="form-success">Thanks — your message has been received. We'll reply soon.</p>
      <?php else: ?>

        <form method="post" action="contact.php" class="auth-form" novalidate>
           <?php echo csrf_field(); ?>
          <label>
            Name
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
            Message
            <textarea name="message" rows="5"
                      class="<?php echo isset($errors['message']) ? 'has-error' : ''; ?>"
                      placeholder="How can we help?"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            <?php if (isset($errors['message'])): ?>
              <span class="field-error"><?php echo $errors['message']; ?></span>
            <?php endif; ?>
          </label>

          <button type="submit" class="btn-primary btn-full">SEND MESSAGE →</button>
        </form>

      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>