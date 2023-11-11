<?php
// Include database connection file
include_once 'connect.php';
// Get sort parameter from URL
$sortOption = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);

// Define default sort and order
$sort = 'product_name'; // default sort field
$order = 'asc'; // default order

// Map sort options to fields and order
$sortOptions = [
    'product_name_asc' => ['field' => 'product_name', 'order' => 'asc'],
    'product_name_desc' => ['field' => 'product_name', 'order' => 'desc'],
    'price_asc' => ['field' => 'price', 'order' => 'asc'],
    'price_desc' => ['field' => 'price', 'order' => 'desc'],
    // Add similar mappings for ratings if available
];

if (array_key_exists($sortOption, $sortOptions)) {
    $sort = $sortOptions[$sortOption]['field'];
    $order = $sortOptions[$sortOption]['order'];
}

// SQL query
try {
    $sql = "SELECT product_id, product_name, description, price, inventory_count, image FROM products ORDER BY $sort $order";
    $stmt = $db->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $products = []; // Set products to an empty array on error.
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

        <div class="sorting-options">
    <form action="" method="get">
        <label for="sorting">Sort by:</label>
        <select name="sort" id="sorting" onchange="this.form.submit()">
            <option value="product_name_asc">Name (A-Z)</option>
            <option value="product_name_desc">Name (Z-A)</option>
            <option value="price_asc">Price (Low to High)</option>
            <option value="price_desc">Price (High to Low)</option>
            <!-- Add similar options for ratings if available -->
        </select>
    </form>
</div>


        <div class="listOfProducts">
            <?php foreach ($products as $product): ?>
                <div class="productItem">
                    <div class="productImage">
                        <a href="product.php?id=<?= htmlspecialchars($product['product_id']) ?>">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
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

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var selectElement = document.getElementById('sorting');

        selectElement.addEventListener('change', function () {
            this.form.submit(); // Submit the form when the selection changes
        });
    });
</script>

</html>