<?php

session_start();


require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_SANITIZE_NUMBER_INT);
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);

    try {

        $stmt = $db->prepare("DELETE FROM reviews WHERE review_id = :review_id");
        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->execute();


        $_SESSION['message'] = "Review deleted successfully!";
        header("Location: product.php?id=$product_id");
        exit;
    } catch (PDOException $e) {
        
        $_SESSION['error_message'] = "Failed to delete the review: " . $e->getMessage();
        header("Location: product.php?id=$product_id");
        exit;
    }
} else {
    
    header("Location: index.php");
    exit;
}
?>
