<?php
require 'connect.php'; // Your database connection file

try {
    // Fetch all users' passwords
    $stmt = $db->query("SELECT user_id, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        // Hash the password with a new salt
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        // Prepare the update statement
        $updateStmt = $db->prepare("UPDATE users SET password = :hashedPassword WHERE user_id = :userId");
        $updateStmt->execute([
            ':hashedPassword' => $hashedPassword,
            ':userId' => $user['user_id']
        ]);
    }

    echo "Passwords updated successfully.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>