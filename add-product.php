<?php

session_start();


require 'connect.php';


if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','content_manager', 'sales_manager'])) {
    die('You do not have permission to view this page.');
}



$product_name = '';
$description = '';
$price = '';
$inventory_count = '';
$image = '';
$category_id = '';

$categories = $db->query("SELECT category_id, category_name FROM categories")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $inventory_count = filter_input(INPUT_POST, 'inventory_count', FILTER_SANITIZE_NUMBER_INT);
    $product_type = filter_input(INPUT_POST, 'product_type', FILTER_SANITIZE_STRING);

    

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $file_name = $_FILES['image']['name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];


        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) die('Error: Please select a valid file format.');


        if (in_array($file_type, $allowed)) {

            if (file_exists("images/" . $file_name)) {
                echo $file_name . ' is already exists.';
            } else {
                move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $file_name);
                echo 'Your file was uploaded successfully.';
                $image = $file_name;
            }
        } else {
            echo 'Error: There was a problem uploading your file. Please try again.';
        }
    } else {
        echo 'Error: ' . $_FILES['image']['error'];
    }

     if (isset($_POST['category']) && $_POST['category'] === 'new' && !empty($_POST['new_category_name'])) {
        $newCategoryName = filter_input(INPUT_POST, 'new_category_name', FILTER_SANITIZE_STRING);
        $insertCategoryStmt = $db->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $insertCategoryStmt->execute([$newCategoryName]);
        $category_id = $db->lastInsertId();
    } else {
        $category_id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    }

    if ($inventory_count < 0)
    {
        die('Error: Inventory count cannot be listed as negative.');
    }

    if ($price < 0)
    {
        die('Error: Price cannot be lower than 0.');
    }


    if ($image) {
        try {
            $stmt = $db->prepare("INSERT INTO products (product_name, description, price, inventory_count, image, category_id) VALUES (:product_name, :description, :price, :inventory_count, :image, :category_id)");
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':inventory_count', $inventory_count);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            echo "<script>alert('Product Added Successfully!'); window.location.href='products.php';</script>"; 
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - LUMi</title>
        <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>
<body id="add-product">

<?php include 'header.php';?>

<form action="add-product.php" method="post" enctype="multipart/form-data" id="add-product-form">
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" id="product_name" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" required><br>

    <label for="inventory_count">Inventory Count:</label>
    <input type="number" name="inventory_count" id="inventory_count" required><br>

    <label for="image">Image:</label>
    <input type="file" name="image" id="image" required><br>


    <label for="category">Category:</label>
    <select name="category" id="category" required onchange="showNewCategoryInput()">
        <option value="">Select a category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= ($category['category_id']) ?>"><?= ($category['category_name']) ?></option>
        <?php endforeach; ?>
        <option value="new">Add new category...</option>
    </select><br>


    <div id="new-category" style="display:none;">
        <label for="new_category_name">New Category Name:</label>
        <input type="text" name="new_category_name" id="new_category_name"><br>
    </div>


    <input type="submit" value="Add Product">
</form>

<?php include 'footer.php';?>

<script>

function showNewCategoryInput() {
    var categorySelect = document.getElementById('category');
    var newCategoryInput = document.getElementById('new-category');
    if (categorySelect.value === 'new') {
        newCategoryInput.style.display = 'block';
    } else {
        newCategoryInput.style.display = 'none';
    }
}
</script>

</body>
</html>
