<?php

session_start();


require 'connect.php';


if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'content_manager', 'sales_manager'])) {
    header('Location: products.php');
}


$product_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$product_name = '';
$description = '';
$price = '';
$inventory_count = '';
$image = '';


if ($product_id) {
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {

            $product_name = $product['product_name'];
            $description = $product['description'];
            $price = $product['price'];
            $inventory_count = $product['inventory_count'];
            $image = $product['image'];
        } else {
            die('Product not found.');
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {

    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $inventory_count = filter_input(INPUT_POST, 'inventory_count', FILTER_VALIDATE_INT);
    

     if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $file_name = $_FILES['image']['name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            die('Error: Please select a valid file format.');
        }

        if (in_array($file_type, $allowed)) {

            
                move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $file_name);
                echo 'Your file was uploaded successfully.';
                $image = $file_name; 
        } else {
            echo 'Error: There was a problem uploading your file. Please try again.';
        }
    } else {
        echo 'Error: ' . $_FILES['image']['error'];
    }
    

    try {
        $stmt = $db->prepare("UPDATE products SET product_name = :product_name, description = :description, price = :price, inventory_count = :inventory_count, image = :image WHERE product_id = :product_id");
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':inventory_count', $inventory_count);
        $stmt->bindParam(':image', $image); 
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        
        echo 'Product updated successfully.';
        header('Location: product.php?id=' . $product_id);
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editing <?= htmlspecialchars($product['product_name']) ?> - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>
<body id= "edit-product">

<?php include 'header.php';?>

<div class= "edit-product-form">
<form action="edit-product.php?id=<?= $product_id ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Do you want to update the product?');">
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($product_name) ?>" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required><?= htmlspecialchars($description) ?></textarea><br>

    <label for="price">Price:</label>
    <input type="text" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required><br>

    <label for="inventory_count">Inventory Count:</label>
    <input type="number" name="inventory_count" id="inventory_count" value="<?= htmlspecialchars($inventory_count) ?>" required><br>

    <label for="image">Image:</label>
    <input type="file" name="image" id="image"><br>
    Current Image: <img src="images/<?= htmlspecialchars($image) ?>" alt="Current Image" width="100"><br>

    <input type="submit" name="update_product" class= "update-product-button" value="Update Product">
</form>

<?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'content_manager')): ?>
<form action="delete-product.php" method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
    <input type="submit" name="delete_product" class="delete-product-button" value="Delete Product">
</form>
<?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>