<?php
// adminUsers.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Fetch all users
$stmt = $conn->prepare('SELECT user_id, name, email, date_registered FROM users');
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - NeighbourGoods Admin</title>
    <!-- Include your CSS and other headers -->
</head>
<body>
    <!-- Include your admin navbar here -->
    <h1>User Management</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Date Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['date_registered']); ?></td>
                <td>
                    <a href="adminUserEdit.php?id=<?php echo $user['user_id']; ?>">Edit</a>
                    <a href="adminUserDelete.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
