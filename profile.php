<?php

session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}


require 'connect.php'; 

try {
    
    $userId = $_SESSION['user_id']; 
    $userQuery = "SELECT first_name, username, email FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($userQuery);
    $statement->execute(['user_id' => $userId]);
    
    
    $userData = $statement->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Profile - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>


<body id="profile">

 <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>

<?php include 'header.php'; ?>

<main class= "main-profile">
<h2>Profile </h2>
<div class="profile-container">
    <form action="upload-profile-picture.php" method="post" enctype="multipart/form-data">
    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="profile_picture" id="profile_picture" required>
    <input type="submit" value="Upload Picture" name="submit">
</form>

<h1>🟆 Welcome, <?= ($userData['first_name']); ?> 🟆</h1>
    <p>Email: <?= ($userData['email']); ?></p>
    <p>Username: <?= ($userData['username']); ?></p>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="manage-employees.php" class="manage-employees-button">Manage Employees</a>
    <?php endif; ?>
</div>

<form action="logout.php" method="post" class="logout-form">
    <input type="submit" name="logout" value="Logout" class="logout-button">
</form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

