<?php
// dashboard.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
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

// Fetch quick stats for dashboard overview
// Total Donations Made
$stmt = $conn->prepare('SELECT COUNT(*) FROM food_listings WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($total_donations);
$stmt->fetch();
$stmt->close();

// Active Listings
$stmt = $conn->prepare('SELECT COUNT(*) FROM food_listings WHERE user_id = ? AND status = "available"');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($active_listings);
$stmt->fetch();
$stmt->close();

// Pending Requests (for your listings)
$stmt = $conn->prepare('SELECT COUNT(*) FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id WHERE f.user_id = ? AND r.status = "pending"');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($pending_requests_for_listings);
$stmt->fetch();
$stmt->close();

// Pending Requests Made by You
$stmt = $conn->prepare('SELECT COUNT(*) FROM requests WHERE user_id = ? AND status = "pending"');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($pending_requests_made_by_you);
$stmt->fetch();
$stmt->close();

// Total Requests Made by You
$stmt = $conn->prepare('SELECT COUNT(*) FROM requests WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($total_requests_made);
$stmt->fetch();
$stmt->close();

// Active Requests Made by You
$stmt = $conn->prepare('SELECT COUNT(*) FROM requests WHERE user_id = ? AND status = "pending"');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($active_requests);
$stmt->fetch();
$stmt->close();

// Impact Summary (number of unique users who requested your listings)
$stmt = $conn->prepare('SELECT COUNT(DISTINCT r.user_id) FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id WHERE f.user_id = ? AND r.status = "approved"');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($unique_requesters);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="User Dashboard for NeighbourGoods.">

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
            background-color: #FEFAE0;
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
        /*** Navbar ***/
        .navbar .dropdown-toggle::after {
            border: none;
            content: "\f107";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            vertical-align: middle;
            margin-left: 8px;
        }

        .navbar-dark .navbar-nav .nav-link {
            margin-right: 30px;
            padding: 25px 0;
            color: #FEFAE0; /* Your link color */
            font-size: 15px;
            font-weight: 500;
            text-transform: uppercase;
            outline: none;
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #DDA15E; /* Hover and active link color */
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .navbar-dark .navbar-nav .nav-link  {
                margin-right: 0;
                padding: 10px 0;
            }

            .navbar-dark .navbar-nav {
                border-top: 1px solid #EEEEEE;
            }
        }

        /* Navbar brand adjustments */
        .navbar-dark .navbar-brand {
            height: 75px;
            color: #FEFAE0;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
        }

        /* Navbar toggler adjustments */
        .navbar-dark .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-dark .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30'     xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28%255, %255, %255, 0.5%29'     stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4     15h22M4 23h22'/ %3E%3C/svg%3E");
        }

        /* Sticky Navbar Transition */
        .navbar-dark.sticky-top {
            top: 0;
            transition: top 0.3s;
        }

        /* Additional styles if needed */


        /* Show Menu When Active */
        .navbar.active ul {
            display: flex;
        }
        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px); /* Adjusting for navbar height */
            margin-top: 80px; /* Adjusting for navbar height */
        }
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #FEFAE0;
            padding: 30px 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            border-right: 1px solid #DDA15E;
            position: relative;
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .sidebar h3 {
            text-align: center;
            color: #606C38;
            margin-bottom: 10px;
            font-family: 'Libre Baskerville', serif;
        }
        .sidebar p {
            text-align: center;
            color: #666666;
            margin-bottom: 10px;
            font-family: 'Neuton', serif;
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
            color: #283618;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px 15px;
            border-radius: 25px;
            transition: background 0.3s, color 0.3s;
            font-family: 'Neuton', serif;
            border: 1px solid #DDA15E;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            background: #BC6C25;
            color: #FEFAE0;
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
            /* background-color: #FEFAE0; */
            overflow: hidden;
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
            margin-bottom: 20px;
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
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1001; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #FEFAE0;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #DDA15E;
            border-radius: 15px;
            width: 80%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .close-modal {
            color: #606C38;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #BC6C25;
        }
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
            transition: color 0.3s;
            text-decoration: none;
        }
        .icons-menu a:hover,
        .icons-menu a.active {
            color: #DDA15E;
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
        .btn {
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
        .btn:hover {
            background: #DDA15E;
        }
        /* Forms */
        .settings-form label,
        .add-donation-form label {
            display: block;
            margin-bottom: 5px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        .settings-form input,
        .settings-form textarea,
        .add-donation-form input,
        .add-donation-form textarea {
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
        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                box-shadow: none;
                border-right: none;
                border-bottom: 1px solid #DDA15E;
            }
            .main-content {
                padding: 20px;
            }
            .icons-menu {
                flex-direction: row;
                top: auto;
                bottom: 20px;
                right: 50%;
                transform: translateX(50%);
            }
            .icons-menu a {
                margin: 0 15px;
            }
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
            <h2 class="logo" style="color: #FEFAE0;">NeighbourGoods</h2>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <img src="img/icons8-menu.png" alt="Menu" style="width: 30px; height: 30px;">
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="explore.php" class="nav-item nav-link">Explore</a>
                <a href="dashboard.php" class="nav-item nav-link active">Dashboard</a>
                <a href="userProfile.php" class="nav-item nav-link">Profile</a>
                <a href="logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($profile_picture ?: "img/default-avatar.png", ENT_QUOTES); ?>');"></div>
            <h3><?php echo htmlspecialchars($name); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($bio)); ?></p>
            <ul>
                <li><a href="#overview" class="nav-link active">Dashboard Overview</a></li>
                <li><a href="#browse-donations" class="nav-link">Browse Donations</a></li>
                <li><a href="#manage-donations" class="nav-link">Manage My Donations</a></li>
                <li><a href="#my-requests" class="nav-link">My Requests</a></li>
                <li><a href="#requests-management" class="nav-link">Requests Management</a></li>
                <li><a href="#analytics" class="nav-link">Analytics & Reports</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Overview Section -->
            <div class="content-section active" id="overview">
                <div class="section">
                    <h2>Dashboard Overview</h2>
                    <p>Welcome to your dashboard! Here you can manage your donations and requests.</p>
                    <div class="cards-container">
                        <div class="card">
                            <h3>Total Donations Made</h3>
                            <p><?php echo $total_donations; ?></p>
                        </div>
                        <div class="card">
                            <h3>Active Listings</h3>
                            <p><?php echo $active_listings; ?></p>
                        </div>
                        <div class="card">
                            <h3>Pending Requests for Your Listings</h3>
                            <p><?php echo $pending_requests_for_listings; ?></p>
                        </div>
                        <div class="card">
                            <h3>Pending Requests You Made</h3>
                            <p><?php echo $pending_requests_made_by_you; ?></p>
                        </div>
                        <div class="card">
                            <h3>Total Requests Made by You</h3>
                            <p><?php echo $total_requests_made; ?></p>
                        </div>
                        <div class="card">
                            <h3>Active Requests</h3>
                            <p><?php echo $active_requests; ?></p>
                        </div>
                        <div class="card">
                            <h3>Unique Users Helped</h3>
                            <p><?php echo $unique_requesters; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Browse Donations Section -->
            <div class="content-section" id="browse-donations">
                <div class="section">
                    <h2>Browse Donations</h2>
                    <!-- Display available donations -->
                    <?php
                    $stmt = $conn->prepare('SELECT f.*, u.name AS donor_name FROM food_listings f JOIN users u ON f.user_id = u.user_id WHERE f.status = "available" AND f.user_id != ?');
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Quantity</th>
                                <th>Expiration Date</th>
                                <th>Pickup Location</th>
                                <th>Donor</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['expiration_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td>
                                    <a href="request_item.php?listing_id=<?php echo $row['listing_id']; ?>" class="btn">Request</a>
                                </td>
                            </tr>
                            <?php endwhile; $stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Manage Donations Section -->
            <div class="content-section" id="manage-donations">
                <div class="section">
                    <h2>Manage My Donations</h2>
                    <a href="#" class="btn" id="add-donation-btn">Add New Donation</a>

                    <!-- Add Donation Form (Initially Hidden) -->
                    <div id="add-donation-form" style="display: none;">
                        <form class="add-donation-form" action="add_donation.php" method="POST" enctype="multipart/form-data">
                            <label for="item_description">Item Description:</label>
                            <textarea id="item_description" name="item_description" rows="4" required></textarea>

                            <label for="quantity">Quantity:</label>
                            <input type="text" id="quantity" name="quantity" required>

                            <label for="expiration_date">Expiration Date:</label>
                            <input type="date" id="expiration_date" name="expiration_date">

                            <label for="pickup_location">Pickup Location:</label>
                            <input type="text" id="pickup_location" name="pickup_location" required>

                            <label for="item_image">Item Image:</label>
                            <input type="file" id="item_image" name="item_image" accept="image/*">

                            <button type="submit" class="btn">Submit Donation</button>
                        </form>
                    </div>

                    <!-- Display user's donations -->
                    <!-- Display user's donations -->
                    <?php
                    // Fetch user's donations
                    $stmt = $conn->prepare('SELECT * FROM food_listings WHERE user_id = ?');
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <h3>Your Donations</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Quantity</th>
                                <th>Expiration Date</th>
                                <th>Pickup Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['expiration_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                <td>
                                    <button class="btn edit-btn"
                                        data-donation-id="<?php echo $row['listing_id']; ?>"
                                        data-item-description="<?php echo htmlspecialchars($row['item_description'], ENT_QUOTES); ?>"
                                        data-quantity="<?php echo htmlspecialchars($row['quantity'], ENT_QUOTES); ?>"
                                        data-expiration-date="<?php echo htmlspecialchars($row['expiration_date'], ENT_QUOTES); ?>"
                                        data-pickup-location="<?php echo htmlspecialchars($row['pickup_location'], ENT_QUOTES); ?>"
                                        >Edit</button>
                                        <a href="delete_donation.php?listing_id=<?php echo $row['listing_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this donation?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; $stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Edit Donation Modal -->
            <div id="edit-donation-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" id="close-edit-modal">&times;</span>
                    <h2>Edit Donation</h2>
                    <form id="edit-donation-form" action="edit_donation.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="listing_id" id="edit-listing-id">

                        <label for="edit-item_description">Item Description:</label>
                        <textarea id="edit-item_description" name="item_description" rows="4" required></textarea>

                        <label for="edit-quantity">Quantity:</label>
                        <input type="text" id="edit-quantity" name="quantity" required>

                        <label for="edit-expiration_date">Expiration Date:</label>
                        <input type="date" id="edit-expiration_date" name="expiration_date">

                        <label for="edit-pickup_location">Pickup Location:</label>
                        <input type="text" id="edit-pickup_location" name="pickup_location" required>

                        <label for="edit-item_image">Item Image (Optional):</label>
                        <input type="file" id="edit-item_image" name="item_image" accept="image/*">

                        <button type="submit" class="btn">Update Donation</button>
                    </form>
                </div>
            </div>


            <!-- My Requests Section -->
            <div class="content-section" id="my-requests">
                <div class="section">
                    <h2>My Requests</h2>
                    <!-- Display user's requests -->
                    <?php
                    // Fetch user's requests
                    $stmt = $conn->prepare('SELECT r.*, f.item_description, f.quantity, f.pickup_location, f.expiration_date, u.name AS donor_name FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id JOIN users u ON f.user_id = u.user_id WHERE r.user_id = ?');
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Donor</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td><?php echo date('F j, Y, g:i a', strtotime($row['request_date'])); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <a href="cancel_request.php?request_id=<?php echo $row['request_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to cancel this request?');">Cancel</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; $stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Requests Management Section -->
            <div class="content-section" id="requests-management">
                <div class="section">
                    <h2>Requests Management</h2>
                    <p>Here you can view all the requests you've received from other users for your listings.</p>
                    <!-- Display requests received -->
                    <?php
                    // Fetch requests made to the user's listings
                    $stmt = $conn->prepare('SELECT r.*, f.item_description, u.name AS requester_name FROM requests r JOIN food_listings f ON r.listing_id = f.listing_id JOIN users u ON r.user_id = u.user_id WHERE f.user_id = ? ORDER BY r.request_date DESC');
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Requester</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['requester_name']); ?></td>
                                <td><?php echo date('F j, Y, g:i a', strtotime($row['request_date'])); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <a href="approve_request.php?request_id=<?php echo $row['request_id']; ?>" class="btn">Approve</a>
                                        <a href="decline_request.php?request_id=<?php echo $row['request_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to decline this request?');">Decline</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; $stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics & Reports Section -->
            <div class="content-section" id="analytics">
                <div class="section">
                    <h2>Analytics & Reports</h2>
                    <p>View detailed analytics about your donations and their impact.</p>
                    <?php
                    // Fetch additional analytics if available
                    // Example: Total quantity donated
                    $stmt = $conn->prepare('SELECT SUM(quantity) AS total_quantity FROM food_listings WHERE user_id = ?');
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $stmt->bind_result($total_quantity);
                    $stmt->fetch();
                    $stmt->close();

                    if ($total_quantity):
                    ?>
                        <p><strong>Total Quantity Donated:</strong> <?php echo $total_quantity; ?></p>
                        <!-- You can add more analytics here -->
                    <?php else: ?>
                        <p>No analytics data available at this time.</p>
                    <?php endif; ?>
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

    <!-- JavaScript to Switch Content Sections -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Navigation Links
            const navLinks = document.querySelectorAll('.nav-link');
            // Icons Menu Links
            const iconLinks = document.querySelectorAll('.icon-link');
            // Content Sections
            const contentSections = document.querySelectorAll('.content-section');

            //JavaScript to Switch Content Sections -->
            document.querySelectorAll('.sidebar ul li a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
                    const targetId = this.getAttribute('href').substring(1);
                    document.getElementById(targetId).classList.add('active');
                    document.querySelectorAll('.sidebar ul li a').forEach(link => link.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Toggle Add Donation Form
            const addDonationBtn = document.getElementById('add-donation-btn');
            if (addDonationBtn) {
                addDonationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('add-donation-form');
                    if (form) {
                        form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    }
                });
            }

            // Handle Edit Donation Modal
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModal = document.getElementById('edit-donation-modal');
            const closeEditModal = document.getElementById('close-edit-modal');
            const editDonationForm = document.getElementById('edit-donation-form');

            editButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Get data attributes from the button
                    const donationId = this.getAttribute('data-donation-id');
                    const itemDescription = this.getAttribute('data-item-description');
                    const quantity = this.getAttribute('data-quantity');
                    const expirationDate = this.getAttribute('data-expiration-date');
                    const pickupLocation = this.getAttribute('data-pickup-location');

                    // Populate the form fields
                    document.getElementById('edit-listing-id').value = donationId;
                    document.getElementById('edit-item_description').value = itemDescription;
                    document.getElementById('edit-quantity').value = quantity;
                    document.getElementById('edit-expiration_date').value = expirationDate;
                    document.getElementById('edit-pickup_location').value = pickupLocation;

                    // Show the modal
                    editModal.style.display = 'block';
                });
            });

            // Close the modal when the user clicks on <span> (x)
            closeEditModal.addEventListener('click', function() {
                editModal.style.display = 'none';
            });

            // Close the modal when the user clicks outside of the modal
            window.addEventListener('click', function(event) {
                if (event.target == editModal) {
                    editModal.style.display = 'none';
                }
            });
        });

    </script>
    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>