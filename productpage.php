<?php
// productpage.php
session_start();

// Include your database connection
require 'config.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : 'donation';

if ($type === 'donation' && isset($_GET['listing_id'])) {
    $listing_id = $_GET['listing_id'];

    // Fetch donation details
    $stmt = $conn->prepare('
        SELECT f.*, u.name, u.profile_picture, u.user_id, u.email, u.bio
        FROM food_listings f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.listing_id = ?
    ');
    $stmt->bind_param('i', $listing_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$donation) {
        $_SESSION['error_message'] = 'Donation not found.';
        header('Location: explore.php');
        exit();
    }

    $post_id = $listing_id;

} elseif ($type === 'request' && isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Fetch request details
    $stmt = $conn->prepare('
        SELECT r.*, u.name, u.profile_picture, u.user_id, u.email, u.bio
        FROM requests r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.request_id = ?
    ');
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        $_SESSION['error_message'] = 'Request not found.';
        header('Location: explore.php');
        exit();
    }

    $post_id = $request_id;
} else {
    // Invalid request
    $_SESSION['error_message'] = 'Invalid product.';
    header('Location: explore.php');
    exit();
}

// Handle adding a comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        $stmt = $conn->prepare('INSERT INTO comments (user_id, post_id, post_type, comment_text) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiss', $user_id, $post_id, $type, $comment_text);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: productpage.php?type=$type&" . ($type === 'donation' ? "listing_id=$listing_id" : "request_id=$request_id"));
    exit();
}

// Fetch comments
$stmt = $conn->prepare('
    SELECT c.comment_text, c.created_at, u.name, u.profile_picture, u.user_id
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.post_id = ? AND c.post_type = ?
    ORDER BY c.created_at DESC
');
$stmt->bind_param('is', $post_id, $type);
$stmt->execute();
$comments_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Product Details - NeighbourGoods. Connect, Donate, and Share with Your Community.">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Include the new fonts in your HTML head -->
    <link href="https://fonts.googleapis.com/css2?family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">

    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>

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
        .navbar {
            width: 96.5%;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #283618;
            color: #FEFAE0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            color: #FEFAE0;
            font-family: 'Montserrat', sans-serif;
        }
        .navbar ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .navbar ul li {
            margin-left: 30px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #FEFAE0;
            font-size: 16px;
            transition: color 0.3s;
            font-family: 'Neuton', serif;
        }
        .navbar ul li a:hover {
            color: #DDA15E;
        }
        /* Product Container */
        .product-container {
            max-width: 1000px;
            margin: 120px auto 50px auto;
            background: #FEFAE0;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.3);
            border: 1px solid #DDA15E;
        }
        .product-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .product-header .profile-pic {
            width: 80px;
            height: 80px;
            background: #dddddd;
            border-radius: 50%;
            margin-right: 20px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .product-header h3 {
            margin: 0;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .product-header p {
            margin: 5px 0 0 0;
            color: #666666;
            font-size: 14px;
            font-family: 'Neuton', serif;
        }
        .product-details h2 {
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
            margin-bottom: 20px;
        }
        .product-details p {
            font-size: 16px;
            color: #283618;
            margin-bottom: 10px;
            font-family: 'Neuton', serif;
        }
        .product-details img {
            max-width: 100%;
            margin-top: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        /* Comments Section */
        .comments-section {
            margin-top: 40px;
        }
        .comments-section h3 {
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
            margin-bottom: 20px;
            border-bottom: 2px solid #DDA15E;
            display: inline-block;
            padding-bottom: 5px;
        }
        .comment {
            margin-bottom: 30px;
            background: #FEFAE0;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #DDA15E;
        }
        .comment .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .comment .profile-pic {
            width: 50px;
            height: 50px;
            background: #dddddd;
            border-radius: 50%;
            margin-right: 15px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .comment .comment-header strong {
            color: #283618;
            font-size: 16px;
            font-family: 'Libre Baskerville', serif;
        }
        .comment .comment-header small {
            display: block;
            color: #999999;
            font-size: 12px;
            font-family: 'Neuton', serif;
        }
        .comment .comment-body p {
            margin: 0;
            font-size: 14px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        /* Add Comment */
        .add-comment {
            margin-top: 40px;
        }
        .add-comment h4 {
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
            margin-bottom: 15px;
        }
        .add-comment textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #BC6C25;
            border-radius: 10px;
            resize: none;
            font-family: 'Neuton', serif;
            color: #283618;
            background: #fff;
        }
        .add-comment button {
            margin-top: 15px;
            background: #BC6C25;
            color: #FEFAE0;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.2);
            transition: background 0.3s;
        }
        .add-comment button:hover {
            background: #DDA15E;
        }
        /* Back Button */
        .back-button {
            display: inline-block;
            margin-top: 30px;
            background: #BC6C25;
            color: #FEFAE0;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.2);
            transition: background 0.3s;
        }
        .back-button:hover {
            background: #DDA15E;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .product-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .product-header .profile-pic {
                margin-right: 0;
                margin-bottom: 15px;
            }
            .comment .comment-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .comment .profile-pic {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">NeighbourGoods</div>
        <ul>
            <li><a href="explore.php">Explore</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="userProfile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Product Container -->
    <div class="product-container">
        <div class="product-header">
            <a href="profilepage.php?user_id=<?php echo $type === 'donation' ? $donation['user_id'] : $request['user_id']; ?>">
                <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars(($type === 'donation' ? $donation['profile_picture'] : $request['profile_picture']) ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
            </a>
            <div>
                <h3><a href="profilepage.php?user_id=<?php echo $type === 'donation' ? $donation['user_id'] : $request['user_id']; ?>" style="color: #606C38; text-decoration: none;"><?php echo htmlspecialchars($type === 'donation' ? $donation['name'] : $request['name'], ENT_QUOTES); ?></a></h3>
                <p><?php echo htmlspecialchars($type === 'donation' ? $donation['email'] : $request['email'], ENT_QUOTES); ?></p>
            </div>
        </div>

        <div class="product-details">
            <h2><?php echo $type === 'donation' ? 'Donation Details' : 'Request Details'; ?></h2>
            <?php if ($type === 'donation'): ?>
                <p><strong>Item:</strong> <?php echo htmlspecialchars($donation['item_description'], ENT_QUOTES); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($donation['quantity'], ENT_QUOTES); ?></p>
                <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($donation['pickup_location'], ENT_QUOTES); ?></p>
                <p><strong>Expires on:</strong> <?php echo date('F j, Y', strtotime($donation['expiration_date'])); ?></p>
                <?php if ($donation['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($donation['image_path'], ENT_QUOTES); ?>" alt="Item Image">
                <?php endif; ?>
            <?php else: ?>
                <p><strong>Requested Item:</strong> <?php echo htmlspecialchars($request['item_description'], ENT_QUOTES); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($request['quantity'], ENT_QUOTES); ?></p>
                <?php if ($request['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($request['image_path'], ENT_QUOTES); ?>" alt="Item Image">
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h3>Comments</h3>
            <?php if ($comments_result->num_rows > 0): ?>
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <div class="comment">
                    <div class="comment-header">
                        <a href="profilepage.php?user_id=<?php echo $comment['user_id']; ?>">
                            <div class="profile-pic" style="background-image: url('<?php echo htmlspecialchars($comment['profile_picture'] ?: 'img/default-avatar.png', ENT_QUOTES); ?>');"></div>
                        </a>
                        <div>
                            <strong><a href="profilepage.php?user_id=<?php echo $comment['user_id']; ?>" style="color: #283618; text-decoration: none;"><?php echo htmlspecialchars($comment['name'], ENT_QUOTES); ?></a></strong>
                            <small><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                        </div>
                    </div>
                    <div class="comment-body">
                        <p><?php echo nl2br(htmlspecialchars($comment['comment_text'], ENT_QUOTES)); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <!-- Add Comment Form -->
            <div class="add-comment">
                <h4>Add a Comment</h4>
                <form action="" method="POST">
                    <textarea name="comment_text" required placeholder="Write your comment here..."></textarea>
                    <button type="submit" name="add_comment">Post Comment</button>
                </form>
            </div>
        </div>

        <!-- Back Button -->
        <a href="explore.php" class="back-button">Back to Explore</a>
    </div>

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

</body>
</html>