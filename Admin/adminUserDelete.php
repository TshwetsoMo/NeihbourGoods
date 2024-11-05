<?php
// adminUserDelete.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch user data without 'role'
    $stmt = $conn->prepare('SELECT name, email FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    $stmt->fetch();
    $stmt->close();

    if (!$name) {
        $_SESSION['error_message'] = 'User not found.';
        header('Location: adminDashboard.php');
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete user
    $user_id = intval($_POST['user_id']);

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'You cannot delete your own account.';
        header('Location: adminDashboard.php');
        exit();
    }

    // Delete user from database
    $stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'User deleted successfully.';
        header('Location: adminDashboard.php');
        exit();
    } else {
        $error = 'Failed to delete user.';
    }
    $stmt->close();
} else {
    header('Location: adminDashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete User - NeighbourGoods Admin</title>
    <!-- Include your CSS and other headers -->
    <style>
        /* Add your CSS styles here */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #FEFAE0;
            color: #283618;
            margin: 0;
            padding: 20px;
        }
        .error {
            color: #E65C50;
            margin-bottom: 20px;
        }
        .confirmation {
            max-width: 600px;
            margin: 0 auto;
        }
        .confirmation p {
            font-size: 18px;
        }
        .confirmation form {
            margin-top: 20px;
        }
        .confirmation form button {
            padding: 12px 20px;
            background: #E65C50;
            color: #FEFAE0;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .confirmation form button:hover {
            background: #D9534F;
        }
        .confirmation a {
            margin-left: 20px;
            color: #283618;
            text-decoration: none;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <!-- Include your admin navbar here -->

    <h1>Delete User</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($name)): ?>
        <div class="confirmation">
            <p>Are you sure you want to delete the user <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($email); ?>)?</p>
            <form action="adminUserDelete.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <button type="submit">Yes, Delete User</button>
                <a href="adminDashboard.php">Cancel</a>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
