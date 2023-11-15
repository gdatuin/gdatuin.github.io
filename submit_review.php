<?php
session_start();
require 'connect.php'; // Make sure this file sets up the $db variable correctly.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is logged in or not
    $user_id = $_SESSION['user_id'] ?? null;
    $guest_name = $user_id ? null : filter_input(INPUT_POST, 'guest_name', FILTER_SANITIZE_STRING);

    // Sanitize and validate input
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
    $review_text = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Handle the uploaded image if necessary
  $imageFileName = null;
}
if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
    $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    $file_name = $_FILES['review_image']['name'];
    $file_type = $_FILES['review_image']['type'];
    $file_size = $_FILES['review_image']['size'];

    // Verify file extension
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed)) die('Error: Please select a valid file format.');

    // Verify file type
    if (in_array($file_type, $allowed)) {
        // Check whether file exists before uploading it
        if (file_exists("upload_path/" . $file_name)) {
            echo $file_name . ' is already exists.';
        } else {
            move_uploaded_file($_FILES['review_image']['tmp_name'], "review_images/" . $file_name);
            $imageFileName = $file_name;
        }
    } else {
        echo 'Error: There was a problem uploading your file. Please try again.';
    }
} else {
    echo 'Error: ' . $_FILES['review_image']['error'];
}

try{
    // Insert the review into the database
   if ($user_id) {
    $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text, review_image) VALUES (:product_id, :user_id, :rating, :review_text, :review_image)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
} else {
    $stmt = $db->prepare("INSERT INTO reviews (product_id, guest_name, rating, review_text, review_image) VALUES (:product_id, :guest_name, :rating, :review_text, :review_image)");
    $stmt->bindParam(':guest_name', $guest_name, PDO::PARAM_STR);
}

    // Bind common parameters and execute the statement
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
    $stmt->bindParam(':review_image', $imageFileName, PDO::PARAM_STR);
    $stmt->execute();
        
        // Redirect back to the product page with a success message
        $_SESSION['review_success_message'] = "Review submitted successfully!";
        header("Location: product.php?id=$product_id");
        exit;
    } catch (PDOException $e) {
        // Handle the error, possibly log it and show an error message to the user.
        $_SESSION['review_error_message'] = "Failed to submit the review.";
        header("Location: product.php?id=$product_id");
        exit;
    }
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to the homepage or show an error if the script is accessed without posting form data
    header("Location: index.php");
    exit;
}
?>
