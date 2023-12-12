<?php
session_start();
require 'connect.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}


function fetchEmployees($db) {
     $stmt = $db->prepare("SELECT user_id, CONCAT(last_name, ', ', first_name) AS full_name, username, email, role FROM users WHERE role != 'customer'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$employees = fetchEmployees($db);

$roleNames = [
    'admin' => 'Admin',
    'content_manager' => 'Content Manager',
    'sales_manager' => 'Sales Manager',
    'sales_associate' => 'Sales Associate'
];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_role'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $newRole = filter_input(INPUT_POST, 'new_role', FILTER_SANITIZE_STRING);
    $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);

    if (!$newRole) {
        echo "<script>alert('Error: Please choose a role.'); window.location.href='manage-employees.php';</script>";
        exit;
    }

    if ($reason) {
        $updateStmt = $db->prepare("UPDATE users SET role = :new_role WHERE user_id = :user_id");
        $updateStmt->bindParam(':new_role', $newRole);
        $updateStmt->bindParam(':user_id', $userId);
        $updateStmt->execute();
        
       

        echo "<script>alert('Changes Successful!'); window.location.href='manage-employees.php';</script>";  
        exit;
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>
<body id= "manage-employees">

    <?php include 'header.php'; ?>

    <main>
        <div class ="manage-employees-table-container">
        <h2>Manage Employees</h2>

        <a href="add-employee.php" class="add-employee-button">Add Employee</a>
        <a href="terminate-employee.php" class="terminate-employee-button">Terminate Employee</a>
        <table class="manage-employees-table">
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
                            <?php if ($employee['user_id'] != $_SESSION['user_id'] || $_SESSION['role'] !== 'admin'): ?>
                            <form method="post" onsubmit="return confirm('Are you sure you want to apply the following changes?');">
                                <input type="hidden" name="user_id" value="<?= $employee['user_id'] ?>">
                                <select name="new_role">
                                    <option value="">-</option>
                                    <option value="admin">Admin</option>
                                    <option value="content_manager">Content Manager</option>
                                    <option value="sales_manager">Sales Manager</option>
                                    <option value="sales_associate">Sales Associate</option>
                                </select>
                                <textarea name="reason" placeholder="Reason for role change" required></textarea>
                                <button type="submit" name="change_role">Apply Changes</button>
                            </form>
                             <?php else: ?>
                    <span>N/A </span>
                <?php endif; ?>
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
