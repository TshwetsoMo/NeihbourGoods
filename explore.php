<?php
// explore.php
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
$stmt = $conn->prepare('SELECT name, email, address, phone_number, date_registered, bio, profile_picture FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $address, $phone_number, $date_registered, $bio, $profile_picture);
$stmt->fetch();
$stmt->close();

// Handle adding a new donation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donation'])) {
    $item_description = trim($_POST['item_description']);
    $quantity = trim($_POST['quantity']);
    $expiration_date = $_POST['expiration_date'];
    $pickup_location = trim($_POST['pickup_location']);
    $image_path = null;

    // Handle image upload if any
    if (isset($_FILES['donation_image']) && $_FILES['donation_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['donation_image']['tmp_name'];
        $fileName = $_FILES['donation_image']['name'];
        $fileSize = $_FILES['donation_image']['size'];
        $fileType = $_FILES['donation_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name and define allowed extensions
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/food_listings/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $dest_path;
            } else {
                $_SESSION['error_message'] = 'There was an error uploading your image.';
            }
        } else {
            $_SESSION['error_message'] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    if (empty($_SESSION['error_message'])) {
        // Insert the new donation into the database
        $stmt = $conn->prepare('INSERT INTO food_listings (user_id, item_description, quantity, expiration_date, pickup_location, image_path, status) VALUES (?, ?, ?, ?, ?, ?, "available")');
        $stmt->bind_param('isssss', $user_id, $item_description, $quantity, $expiration_date, $pickup_location, $image_path);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Donation added successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to add donation. Please try again.';
        }
        $stmt->close();
    }
    header('Location: explore.php');
    exit();
}

// Handle filter action
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'donations';

// Fetch food listings or posts based on the filter
if ($filter === 'donations') {
    // Fetch available food listings
    $stmt = $conn->prepare('
        SELECT f.listing_id, f.item_description, f.quantity, f.expiration_date, f.pickup_location, f.image_path, f.created_at, u.user_id, u.name, u.profile_picture
        FROM food_listings f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.status = "available"
        ORDER BY f.created_at DESC
    ');
    $stmt->execute();
    $activity_result = $stmt->get_result();
    $stmt->close();
} elseif ($filter === 'posts') {
    // Fetch posts from the database
    $stmt = $conn->prepare('
        SELECT p.post_id, p.content, p.media_path, p.created_at, u.user_id, u.name, u.profile_picture
        FROM posts p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.created_at DESC
    ');
    $stmt->execute();
    $activity_result = $stmt->get_result();
    $stmt->close();
}

// Fetch random users for "People You May Know" section
$stmt = $conn->prepare('
    SELECT u.user_id, u.name, u.profile_picture
    FROM users u
    WHERE u.user_id != ?
    ORDER BY RAND()
    LIMIT 5
');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$friends_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Explore - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Explore NeighbourGoods - Connect, Donate, and Share with Your Community.">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
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
            padding: 10px 10px 0 10px;
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
        

        /* Main Container */
        .main-container {
            width: 100%;
            max-width: 1480px;
            margin: 100px auto 0 auto;
            display: flex;
            gap: 15px;
            padding: 20px;
            overflow: hidden;
        }
        /* Left Sidebar (Profile) */
        .left-sidebar {
            flex: 1;
            max-width: 260px;
            background: #FEFAE0;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
            overflow-y: auto;
            height: calc(100vh - 140px);
        }
        .profile-card {
            text-align: center;
        }
        .profile-card .profile-pic {
            width: 120px;
            height: 120px;
            background: #dddddd;
            border-radius: 50%;
            margin: 0 auto 15px;
            background-image: url('<?php echo htmlspecialchars($profile_picture ?: 'img/default-avatar.png', ENT_QUOTES); ?>');
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .profile-card h3 {
            margin-bottom: 5px;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .profile-card p {
            color: #666666;
            font-size: 14px;
            font-family: 'Neuton', serif;
        }
        .profile-card .bio {
            margin-top: 15px;
            font-size: 14px;
            color: #333333;
            text-align: left;
            font-family: 'Neuton', serif;
        }
        /* Activity Feed */
        .activity-feed {
            flex: 2;
            overflow-y: auto;
            height: calc(100vh - 140px);
            padding-right: 10px;
        }
        .post-box {
            background: #FEFAE0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .post-box input[type="text"],
        .post-box input[type="date"],
        .post-box textarea {
            width: 90%;
            resize: none;
            border: 1px solid #BC6C25;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 10px;
            font-family: 'Neuton', serif;
            color: #283618;
            background: #fff;
        }
        .post-box input::placeholder,
        .post-box textarea::placeholder {
            color: #555;
        }
        .post-box .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .post-box .actions .icons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .post-box .actions button {
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 16px;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .post-box .actions button:hover {
            background: #DDA15E;
        }
        /* File Input and Label Styling */
        .file-input-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }

        .file-label {
            background: #BC6C25;
            color: #FEFAE0;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Neuton', serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s, color 0.3s;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .file-label:hover {
            background: #DDA15E;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .filter-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-buttons button {
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
            font-family: 'Neuton', serif;
            border: 1px solid #DDA15E;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .filter-buttons button.active {
            background: #606C38;
            color: #FEFAE0;
        }
        .filter-buttons button:hover {
            background: #DDA15E;
        }
        .feed {
            margin-bottom: 20px;
        }
        .feed .post {
            background: #FEFAE0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .feed .post .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .feed .post .user-info .profile-pic {
            width: 50px;
            height: 50px;
            background: #dddddd;
            border-radius: 50%;
            margin-right: 10px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .feed .post .user-info .details {
            font-size: 14px;
            font-family: 'Neuton', serif;
        }
        .feed .post .user-info .details h4 {
            margin: 0;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .feed .post .user-info .details p {
            margin: 0;
            color: #999999;
            font-size: 12px;
        }
        .feed .post .content {
            font-size: 14px;
            color: #283618;
            margin-bottom: 15px;
            font-family: 'Neuton', serif;
        }
        .feed .post .content img {
            max-width: 100%;
            border-radius: 15px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .feed .post .actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .feed .post .actions button,
        .feed .post .actions a.btn {
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            padding: 8px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Neuton', serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s, color 0.3s;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .feed .post .actions button:hover,
        .feed .post .actions a.btn:hover {
            background: #DDA15E;
        }
        /* Right Sidebar */
        .right-sidebar {
            flex: 1;
            max-width: 280px;
            overflow-y: auto;
            height: calc(100vh - 140px);
        }
        .section {
            background: #FEFAE0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .section h3 {
            margin-top: 0;
            color: #606C38;
            margin-bottom: 15px;
            font-family: 'Libre Baskerville', serif;
        }
        .section .item {
            margin-bottom: 15px;
        }
        .section .item:last-child {
            margin-bottom: 0;
        }
        .section .item h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #283618;
            font-family: 'Libre Baskerville', serif;
        }
        .section .item p {
            margin: 0;
            font-size: 14px;
            color: #666666;
            font-family: 'Neuton', serif;
        }
        .friends-list {
            display: flex;
            flex-direction: column;
        }
        .friends-list .friend {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .friends-list .friend:last-child {
            margin-bottom: 0;
        }
        .friends-list .friend .profile-pic {
            width: 40px;
            height: 40px;
            background: #dddddd;
            border-radius: 50%;
            margin-right: 10px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .friends-list .friend .name {
            font-size: 14px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #FEFAE0;
        }
        ::-webkit-scrollbar-thumb {
            background: #C1C1C1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #A8A8A8;
        }
        /* Responsive */
        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
            }
            .left-sidebar, .right-sidebar {
                max-width: 100%;
                height: auto;
                overflow: visible;
            }
            .activity-feed {
                height: auto;
                overflow: visible;
            }
            .left-sidebar, .right-sidebar {
                order: 2;
            }
            .activity-feed {
                order: 1;
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
                <a href="explore.php" class="nav-item nav-link active">Explore</a>
                <a href="dashboard.php" class="nav-item nav-link">Dashboard</a>
                <a href="userProfile.php" class="nav-item nav-link">Profile</a>
                <a href="logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Sidebar -->
        <div class="left-sidebar">
            <div class="profile-card">
                <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($profile_picture ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
                <h3><?php echo htmlspecialchars($name, ENT_QUOTES); ?></h3>
                <?php if (!empty($bio)): ?>
                <div class="bio">
                    <p><?php echo nl2br(htmlspecialchars($bio, ENT_QUOTES)); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="activity-feed">
            <!-- Add Donation or Request Box -->
            <?php if ($filter === 'donations'): ?>
            <!-- Add Donation Box -->
            <div class="post-box">
                <form action="explore.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_donation" value="1">
                    <input type="text" name="item_description" placeholder="Item Description" required>
                    <input type="text" name="quantity" placeholder="Quantity" required>
                    <input type="date" name="expiration_date" placeholder="Expiration Date" required>
                    <input type="text" name="pickup_location" placeholder="Pickup Location" required>
                    
                    <!-- Updated File Input and Label -->
                    <div class="file-input-container">
                        <label for="donation_image" class="file-label">
                            <i class="fas fa-image">Upload Image</i> 
                        </label>
                        <input type="file" id="donation_image" name="donation_image" accept=".jpg, .jpeg, .png, .gif" class="file-input">
                    </div></br></br>

                    <div class="actions">
                        <button type="submit">Add Donation</button>
                    </div>
                </form>
            </div>
            <?php elseif ($filter === 'requests'): ?>
            <!-- Make a Request Box -->
            <div class="post-box">
                <form action="explore.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="make_request" value="1">
                    <input type="text" name="item_description" placeholder="Item Description" required>
                    <input type="text" name="quantity" placeholder="Quantity" required>
                    <label for="request_image">
                        <i class="fas fa-image" style="cursor:pointer;"></i> Upload Image
                    </label>
                    <input type="file" id="request_image" name="request_image" accept="image/*" style="display:none;">
                    <div class="actions">
                        <button type="submit">Make Request</button>
                    </div>
                </form>
            </div>
            <?php elseif ($filter === 'posts'): ?>
            <!-- Add Post Box -->
            <div class="post-box">
                <form action="explore.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_post" value="1">
                    <textarea name="post_content" placeholder="What's on your mind?" required></textarea>
                    <label for="post_image">
                        <i class="fas fa-image" style="cursor:pointer;"></i> Upload Image
                    </label>
                    <input type="file" id="post_image" name="post_image" accept="image/*" style="display:none;">
                    <div class="actions">
                        <button type="submit">Post</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <button class="<?php echo ($filter === 'donations') ? 'active' : ''; ?>" onclick="window.location.href='explore.php?filter=donations'">Donations</button>
                <button class="<?php echo ($filter === 'posts') ? 'active' : ''; ?>" onclick="window.location.href='explore.php?filter=posts'">Posts</button>
            </div>

            <!-- Activity Feed Content -->
            <div class="feed">
                <?php if ($filter === 'donations'): ?>
                    <?php while ($listing = $activity_result->fetch_assoc()): ?>
                    <div class="post">
                        <div class="user-info">
                            <a href="profilepage.php?user_id=<?php echo $listing['user_id']; ?>">
                                <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($listing['profile_picture'] ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
                            </a>
                            <div class="details">
                                <h4><a href="profilepage.php?user_id=<?php echo $listing['user_id']; ?>" style="color: #00A859; text-decoration: none;"><?php echo htmlspecialchars($listing['name'], ENT_QUOTES); ?></a></h4>
                                <p><?php echo date('F j, Y, g:i a', strtotime($listing['created_at'])); ?></p>
                            </div>
                        </div>
                        <a href="productpage.php?type=donation&listing_id=<?php echo $listing['listing_id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="content">
                                <p>Item: <?php echo htmlspecialchars($listing['item_description'], ENT_QUOTES); ?></p>
                                <p>Quantity: <?php echo htmlspecialchars($listing['quantity'], ENT_QUOTES); ?></p>
                                <p>Pickup Location: <?php echo htmlspecialchars($listing['pickup_location'], ENT_QUOTES); ?></p>
                                <p>Expires on: <?php echo date('F j, Y', strtotime($listing['expiration_date'])); ?></p>
                                <?php if ($listing['image_path']): ?>
                                    <img src="<?php echo htmlspecialchars($listing['image_path'], ENT_QUOTES); ?>" alt="Item Image">
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="actions">
                            <a href="request_item.php?listing_id=<?php echo $listing['listing_id']; ?>" class="btn">Request Item</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php elseif ($filter === 'posts'): ?>
                    <?php while ($post = $activity_result->fetch_assoc()): ?>
                    <div class="post">
                        <div class="user-info">
                            <a href="profilepage.php?user_id=<?php echo $post['user_id']; ?>">
                                <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($post['profile_picture'] ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
                            </a>
                            <div class="details">
                                <h4><a href="profilepage.php?user_id=<?php echo $post['user_id']; ?>" style="color: #00A859; text-decoration: none;"><?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?></a></h4>
                                <p><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="content">
                            <p><?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES)); ?></p>
                            <?php if ($post['media_path']): ?>
                                <img src="<?php echo htmlspecialchars($post['media_path'], ENT_QUOTES); ?>" alt="Post Image">
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <!-- Optionally add actions for posts -->
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>


        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <!-- People You May Know Section -->
            <div class="section">
                <h3>People You May Know</h3>
                <div class="friends-list">
                    <?php while ($friend = $friends_result->fetch_assoc()): ?>
                    <div class="friend">
                        <a href="profilepage.php?user_id=<?php echo $friend['user_id']; ?>">
                            <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($friend['profile_picture'] ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
                        </a>
                        <div class="name">
                            <a href="profilepage.php?user_id=<?php echo $friend['user_id']; ?>" style="color: #333333; text-decoration: none;">
                                <?php echo htmlspecialchars($friend['name'], ENT_QUOTES); ?>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Events Section -->
            <div class="section">
                <h3>Upcoming Events</h3>
                <?php
                // Fetch upcoming events
                $stmt = $conn->prepare('SELECT event_id, title, event_date, location FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 3');
                $stmt->execute();
                $events_result = $stmt->get_result();
                while ($event = $events_result->fetch_assoc()):
                ?>
                <div class="item">
                    <h4><?php echo htmlspecialchars($event['title'], ENT_QUOTES); ?></h4>
                    <p><?php echo date('F j, Y, g:i a', strtotime($event['event_date'])); ?></p>
                    <p><?php echo htmlspecialchars($event['location'], ENT_QUOTES); ?></p>
                </div>
                <?php endwhile; $stmt->close(); ?>
            </div>

            <!-- Polls Section -->
            <div class="section">
                <h3>Polls & Surveys</h3>
                <?php
                // Fetch active polls
                $stmt = $conn->prepare('SELECT poll_id, question FROM polls WHERE expires_at > NOW() ORDER BY created_at DESC LIMIT 1');
                $stmt->execute();
                $polls_result = $stmt->get_result();
                while ($poll = $polls_result->fetch_assoc()):
                ?>
                <div class="item">
                    <h4><?php echo htmlspecialchars($poll['question'], ENT_QUOTES); ?></h4>
                    <form method="POST" action="vote.php">
                        <input type="hidden" name="poll_id" value="<?php echo $poll['poll_id']; ?>">
                        <?php
                        // Fetch poll options
                        $stmt2 = $conn->prepare('SELECT option_id, option_text FROM poll_options WHERE poll_id = ?');
                        $stmt2->bind_param('i', $poll['poll_id']);
                        $stmt2->execute();
                        $options_result = $stmt2->get_result();
                        while ($option = $options_result->fetch_assoc()):
                        ?>
                        <label>
                            <input type="radio" name="option_id" value="<?php echo $option['option_id']; ?>" required>
                            <?php echo htmlspecialchars($option['option_text'], ENT_QUOTES); ?>
                        </label><br>
                        <?php endwhile; $stmt2->close(); ?>
                        <button type="submit">Vote</button>
                    </form>
                </div>
                <?php endwhile; $stmt->close(); ?>
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
            confirmButtonColor: '#00A859'
        });
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>
    
    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>

