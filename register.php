<?php
session_start();
// Assuming you have a database connection set up and it is included here
require 'connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match. Please try again.";
    } else {

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username; // Where $username is the logged-in user's name or ID.
    
         $role = 'customer';
         $query = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)";

        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':username', $username, PDO::PARAM_STR);
            $statement->bindValue(':password', $password, PDO::PARAM_STR);
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->bindValue(':role', $role, PDO::PARAM_STR);
            $statement->execute();

     header("Location: index.php"); 
            exit();
        } catch (PDOException $e) {

            die("Error: " . $e->getMessage());
        }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>

<body id="login">

<div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>

<?php include 'header.php'; ?>

<main>
        <div id="heading">
        <h1>Register</h1>

        </div>

<form id="form" action="register.php" method="post">

    <ul>
        <li>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>
    </li>
    
    <li>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    </li>

    <li>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>
    </li>
    
    <li>
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br>
    </li>
</ul>
        <p class="center">
        <button type="submit" id="register_btn" class="formButton">Register</button>
        </p>
    </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>