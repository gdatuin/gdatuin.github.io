<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

  
    $username = strtolower(substr($firstName, 0, 1) . substr($lastName, 0, 7));
    $email = $username . '@lumi.ca';
    $password = password_hash($username, PASSWORD_DEFAULT); 

    try {
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (:username, :email, :password, :role, :first_name, :last_name)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->execute();

        echo "<script>alert('Employee added successfully!'); window.location.href='manage-employees.php';</script>";               
        exit;

    } catch (PDOException $e) {

        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee - LUMi</title>
       <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
</head>
<body id="add-employee">

    <?php include 'header.php'; ?>

    <main>
        <h2>Add New Employee</h2>
        <form action="add-employee.php" method="post" id= "add-employee-form">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="content_manager">Content Manager</option>
                <option value="sales_manager">Sales Manager</option>                
                <option value="sales_associate">Sales Associate</option>
            </select><br>

            <input type="submit" value="Add Employee">
        </form>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
