<?php
// Start the session
session_start();

// Include database connection file
require 'connect.php';

// Check if the user is allowed to add products
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','content_manager'])) {
    die('You do not have permission to view this page.');
}

// Initialize variables to store form data
$product_name = '';
$description = '';
$price = '';
$inventory_count = '';
$image = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $inventory_count = filter_input(INPUT_POST, 'inventory_count', FILTER_SANITIZE_NUMBER_INT);
    
    // Check if an image file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $file_name = $_FILES['image']['name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        // Verify file extension
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) die('Error: Please select a valid file format.');

        // Verify file type
        if (in_array($file_type, $allowed)) {
            // Check whether file exists before uploading it
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
    
    // Insert into the database
    if ($image) {
        try {
            $stmt = $db->prepare("INSERT INTO products (product_name, description, price, inventory_count, image) VALUES (:product_name, :description, :price, :inventory_count, :image)");
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':inventory_count', $inventory_count);
            $stmt->bindParam(':image', $image);
            $stmt->execute();
            echo 'Product added successfully.';
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
<body>

<?php include 'header.php';?>

<!-- Product Form -->
<form action="add-product.php" method="post" enctype="multipart/form-data">
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

    <input type="submit" value="Add Product">
</form>

<?php include 'footer.php';?>

</body>
</html>
