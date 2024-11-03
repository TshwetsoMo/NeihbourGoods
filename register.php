<?php
// register.php
session_start();
require 'config.php'; // Include your database connection file

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $bio = trim($_POST['bio']);
    $availability = trim($_POST['availability']);
    $needs = trim($_POST['needs']);

    // Handle profile picture upload
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($file_ext), $allowed_ext)) {
            $profile_picture = 'uploads/' . uniqid() . '.' . $file_ext;
            // Ensure the uploads directory exists
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
        } else {
            $error = 'Invalid file type for profile picture.';
        }
    }

    // Input validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'An account with that email already exists.';
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into database
                $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, address, phone_number, bio, profile_picture, availability, needs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                if (!$stmt) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $stmt->bind_param('sssssssss', $name, $email, $password_hash, $address, $phone_number, $bio, $profile_picture, $availability, $needs);

                    if ($stmt->execute()) {
                        // Registration successful
                        $_SESSION['user_id'] = $stmt->insert_id;
                        header('Location: dashboard.php');
                        exit();
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Register for NeighbourGoods.">
    
    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">
    
    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Neuton', serif;
            background: url('img/carousel-1.jpg') center center/cover no-repeat;
            position: relative;
            color: #283618;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(40, 54, 24, 0.7); /* Overlay color */
            z-index: 0;
        }
        .register-container {
            position: relative;
            z-index: 1;
            background: #FEFAE0;
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .register-container h2 {
            margin-bottom: 30px;
            text-align: center;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .register-container form {
            display: flex;
            flex-direction: column;
        }
        .register-container form label {
            margin-bottom: 5px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        .register-container form input,
        .register-container form textarea {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #BC6C25;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Neuton', serif;
            background: #fff;
            color: #283618;
        }
        .register-container form button {
            padding: 12px;
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .register-container form button:hover {
            background: #DDA15E;
        }
        .register-container .error {
            color: #E65C50;
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Neuton', serif;
        }
        .register-container .login-link {
            margin-top: 20px;
            text-align: center;
            font-family: 'Neuton', serif;
        }
        .register-container .login-link a {
            color: #BC6C25;
            text-decoration: none;
        }
        .register-container .login-link a:hover {
            text-decoration: underline;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .register-container {
                padding: 20px;
                max-height: none;
                height: auto;
            }
            body {
                height: auto;
                padding: 20px 0;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h2>Create an Account</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:<span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" required>
    
            <label for="email">Email Address:<span style="color:red;">*</span></label>
            <input type="email" id="email" name="email" required>
    
            <label for="password">Password:<span style="color:red;">*</span></label>
            <input type="password" id="password" name="password" required>
    
            <label for="confirm_password">Confirm Password:<span style="color:red;">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" required>
    
            <label for="address">Address:</label>
            <input type="text" id="address" name="address">
    
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number">
    
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
    
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="4"></textarea>
    
            <label for="availability">Availability:</label>
            <textarea id="availability" name="availability" rows="3"></textarea>
            
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>

</body>
</html>


