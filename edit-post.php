<?php
session_start();
require_once 'connect.php';

// Check if the user is logged in and has the right role
if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin', 'content_manager'])) {
    header('Location: index.php');
    exit;
}

// Fetch post data from the database
if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        exit('Post not found.');
    }
} else {
    exit('No post ID provided.');
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    // Sanitize and validate the input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_UNSAFE_RAW);



    $userId = $_SESSION['user_id'] ?? null;
    $imageFileName = $post['blog_image']; // Start with the current image

    // Handling file upload
    if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] == 0) {
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $file_name = $_FILES['blog_image']['name'];
        $file_type = $_FILES['blog_image']['type'];
        $file_size = $_FILES['blog_image']['size'];

        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            die('Error: Please select a valid file format.');
        }

        if (in_array($file_type, $allowed)) {
            $imageFileName = basename($file_name); // Sanitize the file name
            move_uploaded_file($_FILES['blog_image']['tmp_name'], "blog_images/" . $imageFileName);
        } else {
            echo 'Error: There was a problem uploading your file. Please try again.';
        }
    } elseif (isset($_POST['delete_image'])) {
        // Delete current image if checkbox is checked
        if (file_exists("blog_images/" . $imageFileName)) {
            unlink("blog_images/" . $imageFileName);
        }
        $imageFileName = null;
    }

    try {
        // Prepare the UPDATE SQL statement
        $sql = "UPDATE blog_posts SET title = :title, content = :content, blog_image = :blog_image WHERE post_id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':blog_image', $imageFileName);
        $stmt->bindParam(':post_id', $postId);

        // Execute the statement and check if the update was successful
        if ($stmt->execute()) {
            // Redirect only after a successful update
            header('Location: index.php');
            exit;
        } else {
            // Handle error if the update was not successful
            echo "Error updating post.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Edit Blog Post - LUMi</title>
    <script src="https://cdn.tiny.cloud/1/wz9szlczadv95wsdss9iakvbxc1xonpwbquqvfqyrlqkixuy/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({ selector: '#content' });
    </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <form action="edit-post.php?post_id=<?= $post['post_id'] ?>" method="post" enctype="multipart/form-data" id = "edit-post-form">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content"><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div>
                <label for="blog_image">Image:</label>
                <input type="file" id="blog_image" name="blog_image">
                <?php if ($post['blog_image']): ?>
                    <img src="blog_images/<?= $post['blog_image'] ?>" alt="Current Image" style="max-width: 200px;">
                    <input type="checkbox" name="delete_image" id="delete_image">
                    <label for="delete_image">Delete current image</label>
                <?php endif; ?>
            </div>
            <div>
                <input type="submit" name="update_post" value="Update Post">
            </form>
                <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'content_manager')): ?>
<form action="delete-post.php" method="post" onsubmit="return confirm('Are you sure you want to delete this post?');">
    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
    <input type="submit" name="delete_post" class="delete-post-button" value="Delete Post">
</form>
<?php endif; ?>
            </div>
        
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>