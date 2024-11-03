<?php
// profilepage.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['user_id'])) {
    $profile_user_id = $_GET['user_id'];

    // Fetch user data from the database
    $stmt = $conn->prepare('SELECT name, email, address, phone_number, date_registered, bio, profile_picture, availability, needs FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $profile_user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $address, $phone_number, $date_registered, $bio, $profile_picture, $availability, $needs);
    if ($stmt->fetch()) {
        // User exists
    } else {
        $_SESSION['error_message'] = 'User not found.';
        header('Location: explore.php');
        exit();
    }
    $stmt->close();
} else {
    // Invalid request
    $_SESSION['error_message'] = 'Invalid user.';
    header('Location: explore.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($name, ENT_QUOTES); ?> - Profile | NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Profile page for NeighbourGoods. View user information and recent activities.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* General Styles */
        body {
            font-family: 'Neuton', serif;
            background-color: #FEFAE0;
            color: #283618;
            margin: 0;
            overflow-x: hidden;
            background-image: url('img/backgroundimg2.jpg');
            background-size: cover;
            background-attachment: fixed;
        }

        /* Navbar */
        .navbar {
            background: #283618;
            color: #FEFAE0;
        }
        .navbar-brand h2 {
            color: #FEFAE0;
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: bold;
        }
        .navbar .nav-link {
            color: #FEFAE0;
            font-size: 16px;
            font-family: 'Neuton', serif;
        }
        .navbar .nav-link:hover { color: #DDA15E; }

        /* Profile Layout */
        .profile-container {
            display: flex;
            flex-direction: row;
            min-height: calc(100vh - 80px);
            margin-top: 80px;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #FEFAE0;
            padding: 30px 20px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border-right: 1px solid #DDA15E;
        }
        .sidebar .profile-pic {
            width: 120px;
            height: 120px;
            background: #dddddd;
            border-radius: 50%;
            margin: 0 auto 20px;
            background-image: url('<?php echo htmlspecialchars($profile_picture ?: "img/default-avatar.png", ENT_QUOTES); ?>');
            background-size: cover;
            background-position: center;
        }
        .sidebar h3 { text-align: center; color: #606C38; font-family: 'Libre Baskerville', serif; }
        .sidebar p { text-align: center; color: #666666; font-family: 'Neuton', serif; }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; }
        .content-section { display: none; }
        .content-section.active { display: block; }
        .section {
            background: #FEFAE0;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .section h2 { font-size: 28px; color: #606C38; margin-bottom: 20px; font-family: 'Libre Baskerville', serif; }
        .section p { font-size: 16px; color: #666666; line-height: 1.6; }

        /* Icons Menu */
        .icons-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .icons-menu a {
            color: #BC6C25;
            font-size: 24px;
            margin: 10px 0;
            text-decoration: none;
        }
        .icons-menu a.active, .icons-menu a:hover { color: #DDA15E; }

        /* Back Button */
        .back-btn {
            display: block;
            width: fit-content;
            margin: 40px auto;
            padding: 10px 20px;
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            border-radius: 25px;
            font-family: 'Neuton', serif;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .back-btn:hover { background: #DDA15E; }

        /* Responsive */
        @media (max-width: 992px) {
            .profile-container { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #DDA15E; }
            .main-content { padding: 20px; }
            .icons-menu {
                flex-direction: row;
                top: auto;
                bottom: 20px;
                right: 50%;
                transform: translateX(50%);
            }
            .icons-menu a { margin: 0 15px; }
        }
    </style>

</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h2>NeighbourGoods</h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item active"><a href="explore.php" class="nav-link">Explore</a></li>
                    <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="polls.php" class="nav-link">Polls</a></li>
                    <li class="nav-item"><a href="userProfile.php" class="nav-link">My Profile</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Container -->
    <div class="profile-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="profile-pic"></div>
            <h3><?php echo htmlspecialchars($name, ENT_QUOTES); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($bio, ENT_QUOTES)); ?></p>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Icons Menu -->
            <div class="icons-menu">
                <a href="#" class="icon-link active" data-target="overview"><i class="fas fa-home"></i></a>
                <a href="#" class="icon-link" data-target="activity"><i class="fas fa-list"></i></a>
            </div>

            <!-- Overview Section -->
            <div class="content-section active" id="overview">
                <div class="section">
                    <h2>Overview</h2>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($name, ENT_QUOTES); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email, ENT_QUOTES); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($date_registered)); ?></p>
                    <?php if (!empty($address)): ?>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($address, ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($phone_number)): ?>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number, ENT_QUOTES); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($availability)): ?>
                        <p><strong>Availability:</strong> <?php echo nl2br(htmlspecialchars($availability, ENT_QUOTES)); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($needs)): ?>
                        <p><strong>Needs:</strong> <?php echo nl2br(htmlspecialchars($needs, ENT_QUOTES)); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="content-section" id="activity">
                <div class="section">
                    <h2>Recent Activity</h2>
                    <ul class="activity-list">
                        <?php
                        // Fetch recent activity
                        $activity_stmt = $conn->prepare('SELECT item_description, quantity, created_at FROM food_listings WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
                        $activity_stmt->bind_param('i', $profile_user_id);
                        $activity_stmt->execute();
                        $activity_result = $activity_stmt->get_result();
                        if ($activity_result->num_rows > 0):
                            while ($activity = $activity_result->fetch_assoc()):
                        ?>
                        <li>
                            <h4><?php echo htmlspecialchars($activity['item_description'], ENT_QUOTES); ?></h4>
                            <p>Quantity: <?php echo htmlspecialchars($activity['quantity'], ENT_QUOTES); ?></p>
                            <p>Posted on <?php echo date('F j, Y, g:i a', strtotime($activity['created_at'])); ?></p>
                        </li>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <p>No recent activity found.</p>
                        <?php
                        endif;
                        $activity_stmt->close();
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Explore Button -->
    <a href="explore.php" class="back-btn">Back to Explore</a>

    <!-- Success or Error Pop-up -->
    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES); ?>',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#BC6C25'
        });
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>

    <!-- JavaScript to Switch Content Sections -->
    <script>
        document.querySelectorAll('.icon-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
                document.querySelectorAll('.icon-link').forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

