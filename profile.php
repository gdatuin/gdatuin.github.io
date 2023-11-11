<?php
// Start the session
session_start();

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include your database connection script
require 'connect.php'; // This file should establish a connection to your database.

try {
    // Retrieve the logged-in user's information
    $userId = $_SESSION['user_id']; // Make sure you have stored user_id in the session when the user logged in
    $userQuery = "SELECT username, email FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($userQuery);
    $statement->execute(['user_id' => $userId]);
    
    // Fetch the user data
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
        <!-- Profile Information -->
        <h1>Welcome, <?= htmlspecialchars($userData['username']); ?></h1>
        <p>Email: <?= htmlspecialchars($userData['email']); ?></p>
        <!-- Add more profile information you wish to show -->

        <!-- Displaying user orders or any other information would be done here, using a similar approach. -->

    </div>

    <!-- Possibly add a logout button -->
    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>
</html>
