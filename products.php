<?php
session_start();

include_once 'connect.php';

$sortOption = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);


$sort = 'product_name'; 
$order = 'asc'; 
$searchTerm = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);


$sortOptions = [
    'product_name_asc' => ['field' => 'product_name', 'order' => 'asc'],
    'product_name_desc' => ['field' => 'product_name', 'order' => 'desc'],
    'price_asc' => ['field' => 'price', 'order' => 'asc'],
    'price_desc' => ['field' => 'price', 'order' => 'desc'],
    'rating_asc' => ['field' => 'average_rating', 'order' => 'asc'],
    'rating_desc' => ['field' => 'average_rating', 'order' => 'desc']

];

if (array_key_exists($sortOption, $sortOptions)) {
    $sort = $sortOptions[$sortOption]['field'];
    $order = $sortOptions[$sortOption]['order'];
}


try {
     $sql = "SELECT p.product_id, p.product_name, p.description, p.price, p.inventory_count, p.image, COALESCE(AVG(r.rating), 0) AS average_rating FROM products p LEFT JOIN reviews r ON p.product_id = r.product_id";
    if ($searchTerm) {
        $sql .= " WHERE p.product_name LIKE :searchTerm";
    }
    $sql .= " GROUP BY p.product_id";
    $sql .= " ORDER BY $sort $order";

    $stmt = $db->prepare($sql);
    if ($searchTerm) {
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Products - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>


    <body id="products">

        <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>
    

        <?php include 'header.php'; ?>

    <main id="productsContent">
        <div class="productsHeader">
            <h1>PRODUCTS</h1>
        </div>

        <div class="search-form-container">
        <form action="" method="get">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Search">
        </form>
</div>

        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager', 'sales_manager'])): ?>
        <a href="add-product.php" class="add-product-button">Add Product</a>
    <?php endif; ?>
    
        <div class="sorting-options">
    <form action="" method="get">
        <label for="sorting">Sort by:</label>
        <select name="sort" id="sorting" onchange="this.form.submit()">
            <option value="product_name_asc" <?php if ($sortOption == 'product_name_asc') echo 'selected'; ?>>Name (A-Z)</option>
            <option value="product_name_desc" <?php if ($sortOption == 'product_name_desc') echo 'selected'; ?>>Name (Z-A)</option>
            <option value="price_asc" <?php if ($sortOption == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
            <option value="price_desc" <?php if ($sortOption == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
            <option value="rating_asc" <?php if ($sortOption == 'rating_asc') echo 'selected'; ?>>Rating (Low to High)</option>
            <option value="rating_desc" <?php if ($sortOption == 'rating_desc') echo 'selected'; ?>>Rating (High to Low)</option>
        </select>
    </form>
</div>


        <div class="listOfProducts">
            <?php foreach ($products as $product): ?>
                <div class="productItem">
                    <div class="productImage">
                        <a href="product.php?id=<?= htmlspecialchars($product['product_id']) ?>">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        </a>
            </div>
                    <div class="productDetails">
                        <p>
                        <a href="product.php?id=<?= htmlspecialchars($product['product_id']) ?>"><?= htmlspecialchars($product['product_name']) ?></a>
                        <br>$<?= htmlspecialchars(number_format($product['price'], 2)) ?></br>
                    </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>


</html>