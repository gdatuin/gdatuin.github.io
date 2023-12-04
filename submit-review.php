<?php
session_start();
require 'connect.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $formData = [
        'guest_name' => filter_input(INPUT_POST, 'guest_name', FILTER_SANITIZE_STRING),
        'product_id' => filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT),
        'rating' => filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT),
        'review_text' => filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'user_id' => filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT)
    ];

    

    $imageFileName = null;
    
    $userCaptcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);
    if (empty($userCaptcha) || $userCaptcha != $_SESSION['captcha']) {
        
        $_SESSION['review_error_message'] = "Incorrect CAPTCHA. Please try again.";
        $_SESSION['form_data'] = $formData;
        
        header('Location: product.php?id=' . $formData['product_id']. '#submit-review-form');
        exit;
    }

    
    
    unset($_SESSION['form_data']);

    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $file_name = $_FILES['review_image']['name'];
        $file_type = $_FILES['review_image']['type'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (!array_key_exists($ext, $allowed)) die('Error: Please select a valid file format.');

        if (in_array($file_type, $allowed)) {
            if (!file_exists("review_images/" . $file_name)) {
                move_uploaded_file($_FILES['review_image']['tmp_name'], "review_images/" . $file_name);
                $imageFileName = $file_name;
            } else {
                echo $file_name . ' already exists.';
            }
        } else {
            echo 'Error: There was a problem uploading your file. Please try again.';
        }
    }

    try {
        if ($user_id) {
            $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text, review_image) VALUES (:product_id, :user_id, :rating, :review_text, :review_image)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare("INSERT INTO reviews (product_id, guest_name, rating, review_text, review_image) VALUES (:product_id, :guest_name, :rating, :review_text, :review_image)");
            $stmt->bindParam(':guest_name', $guest_name, PDO::PARAM_STR);
        }

        $product_id = $formData['product_id'];
        $rating = $formData['rating'];
        $review_text = $formData['review_text'];
        $review_image = $formData['review_image'];

        
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->bindParam(':review_image', $imageFileName, $imageFileName ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $_SESSION['review_success_message'] = "Review submitted successfully!";
        header("Location: product.php?id=$product_id");
        exit;
    } catch (PDOException $e) {
        $_SESSION['review_error_message'] = "Failed to submit the review: " . $e->getMessage();
        header("Location: product.php?id=$product_id");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
