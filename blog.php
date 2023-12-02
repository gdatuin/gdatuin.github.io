<?php

require_once 'connect.php';

function displayBlogPosts($db) {


    // Fetch and display blog posts
    $stmt = $db->query("SELECT * FROM blog_posts ORDER BY post_date DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $imageClass = !empty($row['blog_image']) ? 'with-image' : 'without-image';
        echo '<div class="blog-post ' . $imageClass . '">';
        if (!empty($row['blog_image'])) {
            echo '<img src="blog_images/' . htmlspecialchars($row['blog_image']) . '" alt="Blog image" class="post-image">';
        }
        echo '<div class="post-text">';
        echo '<h2 class="post-title">' . htmlspecialchars($row['title']) . '</h2>';
        echo '<p class="post-date">Posted on ' . htmlspecialchars($row['post_date']) . '</p>';
        echo '<p class="post-content">' . ($row['content']) . '</p>';

        // Check if the session indicates that the user is logged in and has the correct role
         if (isset($_SESSION['loggedin']) && in_array($_SESSION['role'], ['admin', 'content_manager'])) {
            echo '<a href="edit-post.php?post_id=' . $row['post_id'] . '" class="edit-post-button">Edit Post</a>';
        }
        echo '</div>'; // .post-text
        echo '</div>'; // .blog-post
    
    }
}


function displayCreateButton() {
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager'])){
       echo ' <a href="create-post.php" class="create-post-button">Create Post</a>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Blog - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

    <main>
        <div class= "frame-container">
<?php
        if (isset($_SESSION['loggedin']) && in_array($_SESSION['role'], ['admin', 'content_manager'])) {
            displayCreateButton();
        }
        ?>
    
    <div class="blog-container">
    <?=
        displayBlogPosts($db);
        ?>
    </div>
</main>

    <!-- Include footer -->
  

</body>
</html>
