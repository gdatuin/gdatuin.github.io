<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    header('Location: profile.php');
    exit;
}
else {

// Include the database connection
include 'connect.php';

$usernameError = '';
$passwordError = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_username = trim($_POST['username']);
    $form_password = trim($_POST['password']);

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $form_username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($form_password === $row['password']) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect user based on role
            if ($_SESSION['role'] == 'admin') {
                header("location: admin_dashboard.php"); // Redirect to admin dashboard
                exit;
            } else {
                header("location: index.php"); // Redirect to homepage or user dashboard
                exit;
            }
        } else {
        $passwordError = "Invalid password.";
    }
} else {
    $usernameError = "Username does not exist.";
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

<?php include 'header.php';?>

<main>
    <div id="heading">
        <h1>Login</h1>

    </div>
    <form id= "form" action="login.php" method="post">
    <ul>
        <li>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <?php if (!empty($usernameError)) : ?>
        <span class="error-message"><?= htmlspecialchars($usernameError) ?></span>
    <?php endif; ?>
</li>

<li>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <?php if (!empty($passwordError)) : ?>
        <span class="error-message"><?= htmlspecialchars($passwordError) ?></span>
    <?php endif; ?>
</li>
    </ul>
        <p class="center">
        <button type="submit" id="login_btn" class="formButton">Login</button>
        <a href="register.php" id="newuser_btn">New User?</a>
        </p>
    </form>
</main>

<?php include 'footer.php';?>

</body>
</html>