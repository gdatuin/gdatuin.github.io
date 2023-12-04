<?php

session_start();
require 'connect.php';
$product_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$product = [];
$stockStatus = "IN STOCK";
$averageRating = 0;
$reviews = []; 


try {
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
 
        if ($product['inventory_count'] < 1) {
            $stockStatus = "OUT OF STOCK";
        } else {
            $stockStatus = "IN STOCK";

            if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'sales_manager')) {
                $stockStatus .= " (Available: " . $product['inventory_count'] . ")";
            }
        }
    } else {
        $stockStatus = "OUT OF STOCK";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


try {
    $reviewStmt = $db->prepare("
        SELECT r.rating, r.review_id, r.review_text, r.guest_name, r.review_image, r.user_id, u.username
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.product_id = :product_id
        ORDER BY r.review_id DESC
    ");
    $reviewStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $reviewStmt->execute();
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
}


try {
    $ratingStmt = $db->prepare("SELECT AVG(rating) as average_rating FROM reviews WHERE product_id = :product_id");
    $ratingStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $ratingStmt->execute();

    if ($ratingStmt->rowCount() > 0) {
        $ratingResult = $ratingStmt->fetch(PDO::FETCH_ASSOC);
        $averageRating = round($ratingResult['average_rating'], 1); 
    }
} catch (PDOException $e) {
    echo "Error fetching average rating: " . $e->getMessage();
}


$starRatingHtml = str_repeat('&#9733;', floor($averageRating)) . str_repeat('&#9734;', 5 - floor($averageRating));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_name']) ?> - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>
<body id ="productPage">


        <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>
    <?php include 'header.php'; ?>

    <main class="product-page">
        <?php if ($product): ?>
            <div class="product-container">
                <div class="product-image">
                    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                </div>
                <div class="product-details">
                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager', 'sales_manager'])): ?>
    <a href="edit-product.php?id=<?= htmlspecialchars($product_id) ?>" class="edit-product-button">Edit Product</a>
<?php endif; ?>
                    <h1><?= htmlspecialchars($product['product_name']) ?></h1>
                <div class="product-rating">
            <?= $starRatingHtml ?>
            <span class="rating-number">(<?= $averageRating ?>)</span>
        </div>
            
                    <div class= "product-description">
                    <p><?= htmlspecialchars($product['description']) ?></p>
                </div>
                    <p class="price">$<?= htmlspecialchars(number_format($product['price'], 2)) ?></p>
                    <p class="stock-status"><?= $stockStatus ?></p>
                    <?php if ($product['inventory_count'] > 0): ?>
                        <button class= "add-to-cart-button" onclick="addToCart(<?= $product_id ?>)">Add to Cart</button>
                    <?php endif; ?>
                </div>
            </div>


             <section class="product-reviews">
            <h2>Customer Reviews</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <p>Reviewer: <?= !empty($review['username']) ? htmlspecialchars($review['username'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($review['guest_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p>Rating: <?= str_repeat('&#9733;', (int)$review['rating']) . str_repeat('&#9734;', 5 - (int)$review['rating']); ?></p>
                    <p><?= htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8') ?></p>
                     <?php if (!empty($review['review_image'])): ?>
            <img class="review-image" src="review_images/<?= htmlspecialchars($review['review_image']) ?>" alt="Review image">
        <?php endif; ?>

          <?php if (isset($_SESSION['user_id']) && 
   (($_SESSION['role'] == 'admin') || ($_SESSION['user_id'] == $review['user_id']))): ?>
            <form action="delete-review.php" class="delete-button" method="post" onsubmit="return confirm('Are you sure you want to delete the review?');">
                <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['review_id']) ?>">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
                <button type="submit" name="delete_review" class="delete-review-button">Delete Review</button>
            </form>
        <?php endif; ?>
                </div>
            <?php endforeach; ?>
                
            <?php if (isset($_SESSION['review_error_message'])): ?>
    <div class="error-message"><?= $_SESSION['review_error_message']; ?></div>
    <?php unset($_SESSION['review_error_message']); ?>
<?php endif; ?>

                <form action="submit-review.php" method="post" id="submit-review-form" enctype="multipart/form-data">
                     <?php if (!isset($_SESSION['user_id'])): ?>
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="guest_name" value="<?= htmlspecialchars($formData['guest_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    <?php endif; ?>
                    
                    <label for="rating">Your Rating:</label>
                    <select id="rating" name="rating" required>

                        <?php for ($i = 5; $i >= 1; $i--): ?>
                             <option value="<?= $i ?>" <?= (isset($_SESSION['form_data']['rating']) && $_SESSION['form_data']['rating'] == $i) ? 'selected' : '' ?>>
            <?= str_repeat('&#9733;', $i) . str_repeat('&#9734;', 5 - $i) ?>
        </option>
                        <?php endfor; ?>
                    </select>
                    
                    <label for="review_text">Your Review:</label>
                    <textarea id="review_text" name="review_text" required><?= htmlspecialchars($_SESSION['form_data']['review_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                    
                    <label for="review_image">Upload Image:</label>
                    <input type="file" id="review_image" name="review_image" accept="image/*">

                    
                     <label for="captcha">Enter CAPTCHA:</label>
                    <img src="captcha.php" alt="CAPTCHA">
                    <input type="text" id="captcha" name="captcha" required>
                    
                   <br><button type="submit" name="submit_review" class="submit-review-button">Submit Review</button></br>
                    
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?>">
                </form>
            </section>
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>

    
    </main>

    <?php include 'footer.php'; ?>


</body>
</html>
