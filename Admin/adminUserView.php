<?php
// adminUserView.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch user data
    $stmt = $conn->prepare('SELECT name, email, address, phone_number, bio, profile_picture FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $address, $phone_number, $bio, $profile_picture);
    $stmt->fetch();
    $stmt->close();

    if (!$name) {
        $_SESSION['error_message'] = 'User not found.';
        header('Location: adminDashboard.php');
        exit();
    }
} else {
    header('Location: adminDashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User - NeighbourGoods Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Neuton', serif;
            background-color: #FEFAE0;
            color: #283618;
            margin: 0;
            overflow-x: hidden;
            background-image: url('img/backgroundimg2.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Navbar */
        .navbar .logo {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            color: #FEFAE0;
            font-family: 'Montserrat', sans-serif;
        }

        .navbar-dark .navbar-nav .nav-link {
            margin-right: 30px;
            padding: 25px 0;
            color: #FEFAE0;
            font-size: 15px;
            font-weight: 500;
            text-transform: uppercase;
            outline: none;
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #DDA15E;
        }

        /* Profile Content */
        .profile-container {
            max-width: 800px;
            margin: 80px auto;
            background: #FEFAE0;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            background: #dddddd;
            border-radius: 50%;
            margin: 0 auto 20px;
            background-image: url('<?php echo htmlspecialchars($profile_picture ?: "img/default-avatar.png", ENT_QUOTES); ?>');
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .profile-info h2 {
            text-align: center;
            color: #606C38;
            margin-bottom: 10px;
            font-family: 'Libre Baskerville', serif;
        }

        .profile-info p {
            text-align: center;
            color: #666666;
            margin-bottom: 20px;
            font-family: 'Neuton', serif;
        }

        .profile-details {
            margin-top: 30px;
        }

        .profile-details h3 {
            color: #606C38;
            margin-bottom: 20px;
            font-family: 'Libre Baskerville', serif;
        }

        .profile-details .detail {
            margin-bottom: 15px;
            font-family: 'Neuton', serif;
        }

        .profile-details .detail strong {
            color: #283618;
            width: 150px;
            display: inline-block;
        }

        .btn-custom {
            display: inline-block;
            padding: 10px 20px;
            background: #BC6C25;
            color: #FEFAE0;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background: #DDA15E;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

    </style>
</head>
<body style="background-image: url('img/backgroundimg2.jpg');">

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-md navbar-dark sticky-top" style="background-color: #283618;">
        <a class="navbar-brand d-flex align-items-center px-4 px-lg-5" href="#">
            <h2 class="logo" style="color: #FEFAE0;">NeighbourGoods Admin</h2>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <img src="img/icons8-menu.png" alt="Menu" style="width: 30px; height: 30px;">
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="adminDashboard.php" class="nav-item nav-link">Dashboard</a>
                <a href="logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Profile Container -->
    <div class="profile-container">
        <div class="profile-pic"></div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($name); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($bio)); ?></p>
        </div>
        <div class="profile-details">
            <h3>User Details</h3>
            <div class="detail"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
            <div class="detail"><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></div>
            <div class="detail"><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></div>
        </div>
        <div class="actions">
            <a href="adminUserEdit.php?id=<?php echo $user_id; ?>" class="btn-custom">Edit User</a>
            <a href="adminUserDelete.php?id=<?php echo $user_id; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</a>
            <a href="adminDashboard.php" class="btn-custom">Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
