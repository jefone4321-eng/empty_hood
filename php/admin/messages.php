<?php
  include 'admin_header.php';
  $messages = $pdo->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC")->fetchAll();
?>

<h1>Messages</h1>
<p style="color:#888; margin-bottom:24px;">Submissions from the Contact Us form.</p>

<div class="admin-table-wrapper">
<table class="admin-table">
    <tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Actions</th></tr>
    <?php if (empty($messages)): ?>
        <tr><td colspan="5" class="admin-empty">No messages yet.</td></tr>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['name']); ?></td>
                <td><a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a></td>
                <td style="max-width:320px; white-space:pre-wrap;"><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo date("M j, Y g:i A", strtotime($message['submitted_at'])); ?></td>
                <td>
                    <form method="post" action="delete_message.php" onsubmit="return confirm('Delete this message?');" style="display:inline">
                        <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                        <button type="submit" class="admin-delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>

<?php include 'admin_footer.php'; ?>