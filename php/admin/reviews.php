<?php
include 'admin_header.php';
$reviews = $pdo->query("
    SELECT reviews.id, reviews.rating, reviews.review_text, reviews.created_at, accounts.name
    FROM reviews
    JOIN accounts ON reviews.user_id = accounts.id
    ORDER BY reviews.created_at DESC
  ")->fetchAll();
?>

<h1>Reviews</h1>

<table class="admin-table">
    <tr>
        <th>Customer</th>
        <th>Rating</th>
        <th>Review</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($reviews as $review): ?>
        <tr>
            <td><?php echo htmlspecialchars($review['name']); ?></td>
            <td><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></td>
            <td><?php echo htmlspecialchars($review['review_text']); ?></td>
            <td><?php echo date("M j, Y", strtotime($review['created_at'])); ?></td>
            <td>
                <form method="post" action="delete_review.php" onsubmit="return confirm('Delete this review?');"
                    style="display:inline">
                      <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                    <button type="submit" class="admin-delete-btn">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'admin_footer.php'; ?>