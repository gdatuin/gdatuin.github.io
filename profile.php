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

<?php include 'header.php'; ?>

    <div class="profile-container">
       
        <h1>Welcome, <?= ($userData['first_name']); ?></h1>
        <p>Email: <?=($userData['email']); ?></p>
        <p>Username: <?=($userData['username']); ?></p>
 

    </div>

    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>
</html>
