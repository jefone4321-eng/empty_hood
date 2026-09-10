<?php
  include 'admin_header.php';
  $customers = $pdo->query("SELECT id, name, email, is_admin, created_at FROM accounts ORDER BY id DESC")->fetchAll();
?>

<h1>Customers</h1>

<table class="admin-table">
    <tr><th>Name</th><th>Email</th><th>Joined</th><th>Role</th><th>Actions</th></tr>
    <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?php echo htmlspecialchars($customer['name']); ?></td>
            <td><?php echo htmlspecialchars($customer['email']); ?></td>
            <td><?php echo date("M j, Y", strtotime($customer['created_at'])); ?></td>
           <td>
    <?php if ($customer['is_admin']): ?>
        <span class="admin-badge admin-badge-admin">Admin</span>
    <?php else: ?>
        <span class="admin-badge admin-badge-customer">Customer</span>
    <?php endif; ?>
</td>
            <td>
                <?php if ($customer['id'] != $_SESSION['user_id']): ?>
                    <form method="post" action="delete_customer.php" onsubmit="return confirm('Delete this account?');" style="display:inline">
                      <?php echo csrf_field(); ?>    
                    <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                        <button type="submit" class="admin-delete-btn">Delete</button>
                    </form>
                <?php else: ?>
                    <span class="account-since">(you)</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'admin_footer.php'; ?>