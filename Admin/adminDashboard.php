<?php
// adminDashboard.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Include your database connection
require 'config.php';

// Fetch admin data (if needed)
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT name, email, profile_picture FROM users WHERE user_id = ?');
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();

// Fetch key statistics for admin dashboard

// Total Users
$stmt = $conn->prepare('SELECT COUNT(*) FROM users');
$stmt->execute();
$stmt->bind_result($total_users);
$stmt->fetch();
$stmt->close();

// Total Donations
$stmt = $conn->prepare('SELECT COUNT(*) FROM food_listings');
$stmt->execute();
$stmt->bind_result($total_donations);
$stmt->fetch();
$stmt->close();

// Total Requests
$stmt = $conn->prepare('SELECT COUNT(*) FROM requests');
$stmt->execute();
$stmt->bind_result($total_requests);
$stmt->fetch();
$stmt->close();

// Total Posts
$stmt = $conn->prepare('SELECT COUNT(*) FROM posts');
$stmt->execute();
$stmt->bind_result($total_posts);
$stmt->fetch();
$stmt->close();

// Total Comments
$stmt = $conn->prepare('SELECT COUNT(*) FROM comments');
$stmt->execute();
$stmt->bind_result($total_comments);
$stmt->fetch();
$stmt->close();

// Total Events
$stmt = $conn->prepare('SELECT COUNT(*) FROM events');
$stmt->execute();
$stmt->bind_result($total_events);
$stmt->fetch();
$stmt->close();

// Total Polls
$stmt = $conn->prepare('SELECT COUNT(*) FROM polls');
$stmt->execute();
$stmt->bind_result($total_polls);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Admin Dashboard for NeighbourGoods.">

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

        /* Main Content */
        .main-content {
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .section {
            background: #FEFAE0;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
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

        .section p {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            font-family: 'Neuton', serif;
        }

        /* Cards for Overview */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            flex: 1;
            min-width: 200px;
            background: #FEFAE0;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }

        .card h3 {
            font-size: 22px;
            color: #606C38;
            margin-bottom: 10px;
            font-family: 'Libre Baskerville', serif;
        }

        .card p {
            font-size: 36px;
            color: #283618;
            margin: 0;
            font-family: 'Neuton', serif;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-family: 'Neuton', serif;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #DDA15E;
            text-align: left;
            color: #283618;
        }

        table th {
            background: #BC6C25;
            color: #FEFAE0;
            font-family: 'Libre Baskerville', serif;
        }

        table tr:nth-child(even) {
            background: #F0EDE5;
        }

        /* Buttons */
        .btn-custom {
            display: inline-block;
            padding: 10px 20px;
            background: #BC6C25;
            color: #FEFAE0;
            text-decoration: none;
            border-radius: 25px;
            margin-bottom: 20px;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background: #DDA15E;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #283618;
            font-family: 'Neuton', serif;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #BC6C25;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Neuton', serif;
            background: #fff;
            color: #283618;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .cards-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

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
                <a href="adminDashboard.php" class="nav-item nav-link active">Dashboard</a>
                <a href="../index.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Main Content -->
    <div class="main-content">

        <!-- Profile Section -->
        <div class="section">
            <div class="text-center">
                <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($profile_picture ?: "img/default-avatar.png", ENT_QUOTES); ?>'); width: 150px; height: 150px; margin: 0 auto;"></div>
                <h3><?php echo htmlspecialchars($name); ?></h3>
                <p>Administrator</p>
            </div>
        </div>

        <!-- Overview Section -->
        <div class="section">
            <h2>Dashboard Overview</h2>
            <p>Welcome to the admin dashboard! Here you can manage users, donations, requests, and more.</p>
            <div class="cards-container">
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="card">
                    <h3>Total Donations</h3>
                    <p><?php echo $total_donations; ?></p>
                </div>
                <div class="card">
                    <h3>Total Requests</h3>
                    <p><?php echo $total_requests; ?></p>
                </div>
                <div class="card">
                    <h3>Total Posts</h3>
                    <p><?php echo $total_posts; ?></p>
                </div>
                <div class="card">
                    <h3>Total Comments</h3>
                    <p><?php echo $total_comments; ?></p>
                </div>
                <div class="card">
                    <h3>Upcoming Events</h3>
                    <p><?php echo $total_events; ?></p>
                </div>
                <div class="card">
                    <h3>Active Polls</h3>
                    <p><?php echo $total_polls; ?></p>
                </div>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="section">
            <h2>User Management</h2>
            <!-- Display all users -->
            <?php
            $stmt = $conn->prepare('SELECT user_id, name, email, date_registered FROM users');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
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
                            <a href="adminUserView.php?id=<?php echo $user['user_id']; ?>" class="btn-custom">View</a>
                            <a href="adminUserEdit.php?id=<?php echo $user['user_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminUserDelete.php?id=<?php echo $user['user_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>

        <!-- Donation Management Section -->
        <div class="section">
            <h2>Donation Management</h2>
            <!-- Display all donations -->
            <?php
            $stmt = $conn->prepare('SELECT f.listing_id, f.item_description, f.quantity, f.expiration_date, u.name AS donor_name FROM food_listings f JOIN users u ON f.user_id = u.user_id');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Quantity</th>
                        <th>Expiration Date</th>
                        <th>Donor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($donation = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($donation['item_description']); ?></td>
                        <td><?php echo htmlspecialchars($donation['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($donation['expiration_date']); ?></td>
                        <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                        <td>
                            <a href="adminDonationView.php?id=<?php echo $donation['listing_id']; ?>" class="btn-custom">View</a>
                            <a href="adminDonationEdit.php?id=<?php echo $donation['listing_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminDonationDelete.php?id=<?php echo $donation['listing_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this donation?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>

        <!-- Request Management Section -->
        <div class="section">
            <h2>Request Management</h2>
            <!-- Display all requests -->
            <?php
            $stmt = $conn->prepare('SELECT r.request_id, r.status, r.request_date, u.name AS requester_name, f.item_description FROM requests r JOIN users u ON r.user_id = u.user_id JOIN food_listings f ON r.listing_id = f.listing_id');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Requester</th>
                        <th>Item Requested</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['item_description']); ?></td>
                        <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($request['status'])); ?></td>
                        <td>
                            <a href="adminRequestView.php?id=<?php echo $request['request_id']; ?>" class="btn-custom">View</a>
                            <a href="adminRequestEdit.php?id=<?php echo $request['request_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminRequestDelete.php?id=<?php echo $request['request_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this request?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>

        <!-- Post Management Section -->
        <div class="section">
            <h2>Post Management</h2>
            <a href="adminPostCreate.php" class="btn-custom">Add New Post</a>
            <!-- Display all posts -->
            <?php
            $stmt = $conn->prepare('SELECT p.post_id, p.title, p.content, u.name AS author_name FROM posts p JOIN users u ON p.user_id = u.user_id');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($post['content']); ?></td>
                        <td>
                            <a href="adminPostView.php?id=<?php echo $post['post_id']; ?>" class="btn-custom">View</a>
                            <a href="adminPostEdit.php?id=<?php echo $post['post_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminPostDelete.php?id=<?php echo $post['post_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>

        

        <!-- Event Management Section -->
        <div class="section">
            <h2>Event Management</h2>
            <a href="adminEventCreate.php" class="btn-custom">Add New Event</a>
            <!-- Display all events -->
            <?php
            $stmt = $conn->prepare('SELECT event_id, title, event_date, location FROM events');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td>
                            <a href="adminEventEdit.php?id=<?php echo $event['event_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminEventDelete.php?id=<?php echo $event['event_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
        </div>

        <!-- Poll Management Section -->
        <div class="section">
            <h2>Poll Management</h2>
            <a href="adminPollCreate.php" class="btn-custom">Add New Poll</a>
            <!-- Display all polls -->
            <?php
            $stmt = $conn->prepare('SELECT poll_id, question, expires_at FROM polls');
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Expires at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($poll = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($poll['question']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($poll['expires_at'])); ?></td>
                        <td>
                            <a href="adminPollEdit.php?id=<?php echo $poll['poll_id']; ?>" class="btn-custom">Edit</a>
                            <a href="adminPollDelete.php?id=<?php echo $poll['poll_id']; ?>" class="btn-custom" onclick="return confirm('Are you sure you want to delete this poll?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; $stmt->close(); ?>
                </tbody>
            </table>
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

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
