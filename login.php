<?php
// login.php
session_start();
require 'config.php'; // Include your database connection file

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Prepare the SQL statement without role
        $stmt = $conn->prepare('SELECT user_id, password_hash FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Successful login
                session_regenerate_id(true); // Prevent session fixation
                $_SESSION['user_id'] = $user_id;

                // Assume user ID 1 is the admin
                if ($user_id == 1) {
                    $_SESSION['is_admin'] = true;
                    // Redirect to admin dashboard
                    header('Location: ./Admin/adminDashboard.php');
                    exit();
                } else {
                    $_SESSION['is_admin'] = false;
                    // Redirect to user dashboard
                    header('Location: dashboard.php');
                    exit();
                }

            } else {
                $error = 'Incorrect password.';
            }
        } else {
            $error = 'No account found with that email.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Login to your NeighbourGoods account.">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <!-- Include the new fonts in your HTML head -->
    <link href="https://fonts.googleapis.com/css2?family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">

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
        .login-container {
            position: relative;
            z-index: 1;
            background: #FEFAE0;
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .login-container h2 {
            margin-bottom: 30px;
            text-align: center;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container form label {
            margin-bottom: 5px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        .login-container form input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #BC6C25;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Neuton', serif;
            background: #fff;
            color: #283618;
        }
        .login-container form button {
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
        .login-container form button:hover {
            background: #DDA15E;
        }
        .login-container .error {
            color: #E65C50;
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Neuton', serif;
        }
        .login-container .signup-link {
            margin-top: 20px;
            text-align: center;
            font-family: 'Neuton', serif;
        }
        .login-container .signup-link a {
            color: #BC6C25;
            text-decoration: none;
        }
        .login-container .signup-link a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>

    <div class="login-container">
        <h2>Login to NeighbourGoods</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
        </div>
    </div>

</body>
</html>

