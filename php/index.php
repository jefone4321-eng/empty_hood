        <?php
        session_start ();
         $cartCount = array_sum($_SESSION['cart'] ?? []);
         $cartCount = 0;
         $navStyle  ="nav--transparent";
         $bodyClass = "is-home";
         include 'header.php';
         include 'data.php';

        require '../database/config.php';
$pdo = getConnection();

$stmt = $pdo->query("
    SELECT reviews.rating, reviews.review_text, reviews.created_at, accounts.name
    FROM reviews
    JOIN accounts ON reviews.user_id = accounts.id
    ORDER BY reviews.created_at DESC
    LIMIT 6
");
$reviews = $stmt->fetchAll();


    ?>
    
    
    <section id="home" class="hero">
        <div class="hero-text"><img src ="../images/hLogo.svg">
            <h1>EMPTY HOOD</h1>
            <p class="eyebrow">BUILT DIFFERENT</p>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="php/shop.php" class="btn-primary">SHOP NOW ></a>
            <?php else: ?>
            <a href="signup.php" class="btn-primary">SHOP NOW ></a>
            <?php endif; ?>
        </div>
        <div class="hero-image">
            <img src="../images/hero.svg" alt="Model wearing EH signature hoodie">
            
        </div>
    </section>

    <section class="trust-strip">
        <div class="trust-item">
            <span class="trust-icon">🔒</span>
        <div>
        <p class="trust-title">SECURE PAYMENT</p>
        <p class="trust-sub">100% secure checkout</p>    
    </div>
</div>
    <div class="trust-item">
        <span class="trust-icon">📦</span>
        <div>
            <p class="trust-title">PREMIUM CHECKOUT</p>
            <p class="trust-sub">Built to last. Worn with pride</p>
        </div>
    </div>
        <div class="trust-item">
            <span class="trust-icon">🚚</span>
        <div>
            <p class="trust-title">FAST SHIPPING</p>
            <p class="trust-sub">Ship within 1-3 business days</p>
        </div>
    </div>
        <div class="trust-item">
            <span class="trust-icon">🎧</span>
        <div>
            <p class="trust-title">CUSTOMER SUPPORT</p>
            <p class="trust-sub">We're here to help</p>
        </div> 
        </div>
    </section>

    <section id="shop" class="collections">
        <div class="section-header">
            <h2>EXPLORE COLLECTIONS</h2>
            <a href="#" class="view-all">VIEW ALL →</a>
        </div>

       <div class = "product-grid">
        <?php foreach (array_slice($products, 0, 4) as $product): ?>
            <article class ="product-card">
                <img src = "<?php echo $product["images"]; ?>" alt="<?php echo $product["name"]; ?>">
                <h3><?php echo $product["name"]; ?></h3>
                <p class = "product-price"><?php  echo $product["price"]; ?></p>
        </article>
            <?php endforeach; ?>
        </div>         
        </section>
        <section class="collections">
  <div class="section-header">
    <h2>FEATURED DROPS</h2>
    <a href="#" class="view-all">VIEW ALL →</a>
  </div>

  <div class="product-grid">
   <?php foreach (array_slice($products, 4, 4) as $product): ?>
      <article class="product-card">
        <img src="<?php echo $product["images"]; ?>" alt="<?php echo $product["name"]; ?>">
        <h3><?php echo $product["name"]; ?></h3>
        <p class="product-price"><?php echo $product["price"]; ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>
  
<section id="about" class="our-story">
  <div class="our-story-text">
    <p class="eyebrow">OUR STORY</p>
    <h2>BUILT DIFFERENT.<br>WORN EVERYWHERE.</h2>
    <p class="our-story-body">
      empty-hood is more than just a brand. It's a mindset. A reminder that even when you feel empty inside, you're still built different. We create pieces for those who move in silence, but have a legacy.
    </p>
    <a href="#" class="read-more">READ MORE →</a>
  </div>

  <div class="our-story-images">
    <img src="../images/01_model_side.png" alt="Our story photo 1">
    <img src="../images/02_back_hoodie.png" alt="Our story photo 2">
    <img src="../images/03_model_stairs.png" alt="Our story photo 3">
  </div>
</section>

<section id="lookbook" class="lookbook">
  <div class="section-header">
    <h2>LOOKBOOK</h2>
    <a href="https://instagram.com/empty.hood" class="follow-us" target="_blank">
      FOLLOW US <i class="fa-brands fa-instagram"></i> @empty.hood →
    </a>
  </div>

  <div class="lookbook-grid">
    <img src="../images/04_back_tshirt.png" alt="Lookbook photo 1">
    <img src="../images/05_model_camera.png" alt="Lookbook photo 2">
    <img src="../images/06_back_hoodie_street.png" alt="Lookbook photo 3">
    <img src="../images/07_back_hoodie_close.png" alt="Lookbook photo 4">
    <img src="../images/08_label_patch.png" alt="Lookbook photo 5">
    <img src="../images/09_full_model_street.png" alt="Lookbook photo 6">
  </div>
</section>

<section class="reviews">
    <div class="reviews-header">
        <h2>WHAT THEY SAY</h2>
        <p class="reviews-sub">Real people, Real style, Built Different</p>
    </div>

    <?php if (empty($reviews)): ?>
        <p class="reviews-empty">No reviews yet — be the first to share yours.</p>
    <?php else: ?>
        <div class="reviews-grid">
            <?php foreach ($reviews as $index => $review): ?>
                <div class="review-card <?php echo $index === 1 ? 'featured' : ''; ?>">
                    <div class="review-top">
                        <div class="review-avatar"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <div class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?php echo $i <= $review['rating'] ? 'fa-solid' : 'fa-regular'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="review-date"><?php echo htmlspecialchars($review['name']); ?> · <?php echo date("M j, Y", strtotime($review['created_at'])); ?></p>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="review-form-wrapper">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h3>Leave a Review</h3>
            <form method="post" action="submit_review.php" class="review-form">
                <label>
                    Rating
                    <select name="rating" required>
                        <option value="">Select a rating</option>
                        <option value="5">★★★★★ (5)</option>
                        <option value="4">★★★★☆ (4)</option>
                        <option value="3">★★★☆☆ (3)</option>
                        <option value="2">★★☆☆☆ (2)</option>
                        <option value="1">★☆☆☆☆ (1)</option>
                    </select>
                </label>
                <label>
                    Your Review
                    <textarea name="review_text" rows="4" placeholder="Tell us what you think..." required></textarea>
                </label>
                <button type="submit" class="btn-primary">SUBMIT REVIEW →</button>
            </form>
        <?php else: ?>
            <p class="review-login-prompt"><a href="login.php">Log in</a> to leave a review.</p>
        <?php endif; ?>
    </div>
</section>

<?php if (!isset($_SESSION['user_id'])): ?>
<section class="signup-cta">
  <h2>JOIN THE HOOD</h2>
  <p>Create an account for faster checkout, order history, and early access to drops.</p>
  <a href="signup.php" class="btn-primary">CREATE ACCOUNT →</a>
</section>
<?php endif; ?>





<?php include 'footer.php'; ?>