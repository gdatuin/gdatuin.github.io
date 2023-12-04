<?php
session_start();


require_once 'connect.php';


if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['admin', 'content_manager'])) {
    header('Location: index.php');
}

$imageFileName = null;
$postCreated = false;


if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    $file_name = $_FILES['blog_image']['name'];
    $file_type = $_FILES['blog_image']['type'];
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!array_key_exists($ext, $allowed)) die('Error: Please select a valid file format.');

    if (in_array($file_type, $allowed)) {
        if (!file_exists("blog_images/" . $file_name)) {
            move_uploaded_file($_FILES['blog_image']['tmp_name'], "blog_images/" . $file_name);
            $imageFileName = $file_name;
        } else {
            echo $file_name . ' already exists.';
        }
    } else {
        echo 'Error: There was a problem uploading your file. Please try again.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-post'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_UNSAFE_RAW);
    $userId = $_SESSION['user_id'] ?? null;

    $sql = "INSERT INTO blog_posts (title, content, post_date, user_id, blog_image) VALUES (:title, :content, NOW(), :user_id, :blog_image)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':content', $content);
    $stmt->bindValue(':user_id', $userId);
    $stmt->bindValue(':blog_image', $imageFileName);
    $postCreated = $stmt->execute();

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Create Blog Post - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <script src="https://cdn.tiny.cloud/1/wz9szlczadv95wsdss9iakvbxc1xonpwbquqvfqyrlqkixuy/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({ selector: '#content' });
    </script>
</head>
<body id = "create-post">
    <?php include 'header.php'; ?>

    <main class = "main-create-post">
        <form action="create-post.php" method="post" enctype="multipart/form-data" id= "create-post-form">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content"></textarea>
            </div>
            <div>
                <label for="blog_image">Image:</label>
                <input type="file" id="blog_image" name="blog_image">
            </div>
            <div>
                <input type="submit" name="create-post" value="Create Post">
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
