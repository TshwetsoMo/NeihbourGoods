<?php
// adminUserEdit.php
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
    $stmt = $conn->prepare('SELECT name, email, address, phone_number, bio FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $address, $phone_number, $bio);
    $stmt->fetch();
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update user data
    $user_id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $bio = trim($_POST['bio']);

    // Update the database without 'role'
    $stmt = $conn->prepare('UPDATE users SET name = ?, email = ?, address = ?, phone_number = ?, bio = ? WHERE user_id = ?');
    $stmt->bind_param('sssssi', $name, $email, $address, $phone_number, $bio, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'User updated successfully.';
        header('Location: adminDashboard.php');
        exit();
    } else {
        $error = 'Failed to update user.';
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
    <title>Edit User - NeighbourGoods Admin</title>
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
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #BC6C25;
            border-radius: 5px;
        }
        form button {
            margin-top: 20px;
            padding: 12px;
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background: #DDA15E;
        }
    </style>
</head>
<body>
    <!-- Include your admin navbar here -->

    <h1>Edit User</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="adminUserEdit.php" method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
        
        <label>Phone Number:</label>
        <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
        
        <label>Bio:</label>
        <textarea name="bio"><?php echo htmlspecialchars($bio); ?></textarea>
        
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>

