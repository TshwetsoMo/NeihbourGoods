<?php
// userProfile.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check for login success
$login_success = false;
if (isset($_SESSION['login_success'])) {
    $login_success = true;
    unset($_SESSION['login_success']); // Unset the variable after use
}

// Include your database connection
require 'config.php';

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT name, email, address, phone_number, date_registered, bio, profile_picture, availability, needs FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $address, $phone_number, $date_registered, $bio, $profile_picture, $availability, $needs);
$stmt->fetch();
$stmt->close();

// Fetch recent activity
// Fetch recent donations made by the user
$stmt = $conn->prepare('SELECT item_description, quantity, expiration_date, created_at FROM food_listings WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$donations_result = $stmt->get_result();
$recent_donations = $donations_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch recent requests made by the user
$stmt = $conn->prepare('SELECT r.request_date, r.status, f.item_description, u.name AS donor_name FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id JOIN users u ON f.user_id = u.user_id WHERE r.user_id = ? ORDER BY r.request_date DESC LIMIT 5');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$requests_result = $stmt->get_result();
$recent_requests = $requests_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch recent requests received for the user's listings
$stmt = $conn->prepare('SELECT r.request_date, r.status, f.item_description, u.name AS requester_name FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id JOIN users u ON r.user_id = u.user_id WHERE f.user_id = ? ORDER BY r.request_date DESC LIMIT 5');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$received_requests_result = $stmt->get_result();
$recent_received_requests = $received_requests_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Profile page for NeighbourGoods where users can view and update their profile, check recent activity, and manage their settings.">

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
        /* Profile Page Layout */
        .profile-container {
            display: flex;
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
            border-radius: 50%;
            margin: 0 auto 20px;
            background-image: url('<?php echo htmlspecialchars($profile_picture ?: "img/default-avatar.png", ENT_QUOTES); ?>');
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .sidebar h3 {
            text-align: center;
            color: #606C38;
            margin-bottom: 10px;
            font-family: 'Libre Baskerville', serif;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
            margin-top: 20px;
        }
        .sidebar ul li {
            margin-bottom: 20px;
        }
        .sidebar ul li a {
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 25px;
            font-family: 'Neuton', serif;
            border: 1px solid #DDA15E;
            background: #BC6C25;
            color: #FEFAE0;
            display: block;
            text-align: center;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #DDA15E;
            color: #FEFAE0;
        }
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
            position: relative;
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        .section {
            background: #FEFAE0;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .section h2 {
            font-size: 28px;
            color: #606C38;
            margin-bottom: 20px;
            border-bottom: 2px solid #DDA15E;
            display: inline-block;
            padding-bottom: 5px;
            font-family: 'Libre Baskerville', serif;
        }
        /* Forms */
        .settings-form label {
            display: block;
            margin-bottom: 5px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        .settings-form input,
        .settings-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #BC6C25;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Neuton', serif;
            background: #fff;
            color: #283618;
        }
        .settings-form button {
            padding: 10px 20px;
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
        }
        .settings-form button:hover {
            background: #DDA15E;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .profile-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #DDA15E;
                margin-bottom: 20px;
            }
            
            .main-content {
                padding: 20px;
            }
        }
        .activity-list {
            list-style-type: none;
            padding-left: 0;
        }
        .activity-list li {
            margin-bottom: 15px;
            padding: 10px;
            background: #F0EDE5;
            border-radius: 10px;
            border: 1px solid #DDA15E;
        }
        .activity-list li strong {
            color: #283618;
        }
        .activity-list li small {
            color: #666666;
        }

    </style>

</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark sticky-top" style="background-color: #283618;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h2 class="logo" style="color: #FEFAE0;">NeighbourGoods</h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <img src="img/icons8-menu.png" alt="Menu" style="width: 30px; height: 30px;">
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="explore.php" class="nav-link">Explore</a></li>
                    <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="userProfile.php" class="nav-link active">Profile</a></li>
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
            <h3><?php echo htmlspecialchars($name); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($bio)); ?></p>
            <ul>
                <li><a href="#overview" class="nav-link active">Overview</a></li>
                <li><a href="#activity" class="nav-link">Recent Activity</a></li>
                <li><a href="#settings" class="nav-link">Settings</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Overview Section -->
            <div class="content-section active" id="overview">
                <div class="section">
                    <h2>Overview</h2>
                    <p>Welcome back, <?php echo htmlspecialchars($name); ?>! Here's a summary of your account.</p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($date_registered)); ?></p>
                    <p><strong>Availability:</strong> <?php echo nl2br(htmlspecialchars($availability)); ?></p>
                    <p><strong>Needs:</strong> <?php echo nl2br(htmlspecialchars($needs)); ?></p>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="content-section" id="activity">
                <div class="section">
                    <h2>Recent Activity</h2>
                    <!-- Recent Donations -->
                    <h3>Recent Donations</h3>
                    <?php if (!empty($recent_donations)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_donations as $donation): ?>
                                <li>
                                    <strong>Donated:</strong> <?php echo htmlspecialchars($donation['item_description']); ?> - Quantity: <?php echo htmlspecialchars($donation['quantity']); ?>
                                    <br>
                                    <small>Date Posted: <?php echo date('F j, Y', strtotime($donation['created_at'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>You haven't made any donations recently.</p>
                    <?php endif; ?>

                    <!-- Recent Requests Made -->
                    <h3>Recent Requests Made</h3>
                    <?php if (!empty($recent_requests)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_requests as $request): ?>
                                <li>
                                    <strong>Requested:</strong> <?php echo htmlspecialchars($request['item_description']); ?> from <?php echo htmlspecialchars($request['donor_name']); ?>
                                    <br>
                                    <small>Request Date: <?php echo date('F j, Y', strtotime($request['request_date'])); ?> - Status: <?php echo htmlspecialchars(ucfirst($request['status'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>You haven't made any requests recently.</p>
                    <?php endif; ?>

                    <!-- Recent Requests Received -->
                    <h3>Recent Requests Received</h3>
                    <?php if (!empty($recent_received_requests)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_received_requests as $received_request): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($received_request['requester_name']); ?></strong> requested your item: <?php echo htmlspecialchars($received_request['item_description']); ?>
                                    <br>
                                    <small>Request Date: <?php echo date('F j, Y', strtotime($received_request['request_date'])); ?> - Status: <?php echo htmlspecialchars(ucfirst($received_request['status'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>You haven't received any requests recently.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="content-section" id="settings">
                <div class="section">
                    <h2>Settings</h2>
                    <form class="settings-form" action="update_Profile.php" method="POST" enctype="multipart/form-data">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($bio); ?></textarea>
                        <label for="availability">Availability:</label>
                        <textarea id="availability" name="availability" rows="3"><?php echo htmlspecialchars($availability); ?></textarea>
                        <label for="needs">Needs:</label>
                        <textarea id="needs" name="needs" rows="3"><?php echo htmlspecialchars($needs); ?></textarea>
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        <label for="password">New Password:</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                        <button type="submit">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success or Error Pop-up -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES); ?>',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#00A859'
        });
    </script>
    <?php unset($_SESSION['success_message']); endif; ?>

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

    <!-- JavaScript for Section Navigation -->
    <script>
        document.querySelectorAll('.sidebar ul li a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).classList.add('active');
                document.querySelectorAll('.sidebar ul li a').forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>