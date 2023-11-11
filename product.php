<?php
// Start the session
session_start();

// Include database connection file
require 'connect.php';

// Get product ID from URL
$product_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Initialize a variable to store product data
$product = [];

// Initialize a variable for stock status
$stockStatus = "IN STOCK";

$averageRating = 0;

// Try to fetch the average rating from the database
try {
    $ratingStmt = $db->prepare("SELECT AVG(rating) as average_rating FROM reviews WHERE product_id = :product_id");
    $ratingStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $ratingStmt->execute();

    if ($ratingStmt->rowCount() > 0) {
        $ratingResult = $ratingStmt->fetch(PDO::FETCH_ASSOC);
        $averageRating = round($ratingResult['average_rating'], 1); // Round to 1 decimal place
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$starRatingHtml = '';
$fullStars = floor($averageRating);
$halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
$emptyStars = 5 - $fullStars - $halfStar;

// Echo full stars
for ($i = 0; $i < $fullStars; $i++) {
    $starRatingHtml .= '&#9733;'; // Unicode character for full star
}
// Echo half star if needed
if ($halfStar) {
    $starRatingHtml .= '&#9734;'; // Unicode character for half star (use your preferred half-star character)
}
// Echo empty stars
for ($i = 0; $i < $emptyStars; $i++) {
    $starRatingHtml .= '&#9734;'; // Unicode character for empty star
}

// Try to fetch the product from the database
try {
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if the product exists
    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set stock status based on inventory count
        if (($product['inventory_count'] < 1) && (isset($_SESSION['role']) && ($_SESSION['role'] == 'customer' ) || (($_SESSION['role'] == 'content_manager' )))){
            $stockStatus = "OUT OF STOCK";
            
        } else if ((isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'sales_manager'))) {
            $stockStatus = "Available: " . $product['inventory_count'];
        }
    } else {
        $stockStatus = "OUT OF STOCK";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

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

    <!-- Include header -->
    <?php include 'header.php'; ?>

    <main class="product-page">
        <?php if ($product): ?>
            <div class="product-container">
                <div class="product-image">
                    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                </div>
                <div class="product-details">
                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager'])): ?>
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
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>
    </main>

    <!-- Include footer -->
    <?php include 'footer.php'; ?>


</body>
</html>
