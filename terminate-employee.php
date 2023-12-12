<?php

session_start();
require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

function fetchEmployees($db, $currentUserId) {
    $stmt = $db->prepare("SELECT user_id, CONCAT(last_name, ', ', first_name) AS full_name, username, email, role FROM users WHERE role != 'customer' AND user_id != :current_user_id");
    $stmt->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$roleNames = [
    'admin' => 'Admin',
    'content_manager' => 'Content Manager',
    'sales_manager' => 'Sales Manager',
    'sales_associate' => 'Sales Associate'
];

$currentUserId = $_SESSION['user_id']; 
$employees = fetchEmployees($db, $currentUserId);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['terminate_employee'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);

    if ($reason) {
        $deleteStmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $deleteStmt->bindParam(':user_id', $userId);
        $deleteStmt->execute();

        echo "<script>alert('Employee terminated successfully!'); window.location.href='terminate-employee.php';</script>";  
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terminate Employee - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>
<body id= "terminate-employee">

    <?php include 'header.php'; ?>

    <main>
        <div class ="terminate-employee-table-container">
        <h2>Terminate Employee</h2>

        <table class="terminate-employee-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?= ($employee['full_name']) ?></td>
                        <td><?= ($employee['username']) ?></td>
                        <td><?= ($employee['email']) ?></td>
                        <td><?= $roleNames[$employee['role']] ?></td>
                        <td>
                             <form method="post" onsubmit="return confirm('Are you sure you want to terminate this employee?');">
                            <input type="hidden" name="user_id" value="<?= $employee['user_id'] ?>">
                            <textarea name="reason" placeholder="Reason for termination" required></textarea>
                            <button type="submit" name="terminate_employee">Terminate</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
                </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>

