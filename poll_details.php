<?php
// poll_details.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['poll_id'])) {
    $poll_id = $_GET['poll_id'];

    // Fetch poll details
    $stmt = $conn->prepare('SELECT question FROM polls WHERE poll_id = ? AND expires_at > NOW()');
    $stmt->bind_param('i', $poll_id);
    $stmt->execute();
    $stmt->bind_result($question);
    if ($stmt->fetch()) {
        // Poll exists
    } else {
        $_SESSION['error_message'] = 'Poll not found or has expired.';
        header('Location: polls.php');
        exit();
    }
    $stmt->close();

    // Fetch poll options
    $stmt = $conn->prepare('SELECT option_id, option_text FROM poll_options WHERE poll_id = ?');
    $stmt->bind_param('i', $poll_id);
    $stmt->execute();
    $options_result = $stmt->get_result();
    $stmt->close();

    // Check if user has already voted
    $stmt = $conn->prepare('SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $poll_id, $_SESSION['user_id']);
    $stmt->execute();
    $has_voted = $stmt->get_result()->num_rows > 0;
    $stmt->close();
} else {
    $_SESSION['error_message'] = 'Invalid poll.';
    header('Location: polls.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poll Details - NeighbourGoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="description" content="Participate in community polls on NeighbourGoods.">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Google Fonts -->
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
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        /* Navbar */
        .navbar {
            width: 100%;
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
        /* Poll Container */
        .poll-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
            background: rgba(254, 250, 224, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .poll-container h1 {
            font-size: 32px;
            color: #606C38;
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Libre Baskerville', serif;
        }
        .poll-container form {
            margin-bottom: 20px;
        }
        .poll-container label {
            font-size: 18px;
            color: #283618;
            display: block;
            margin-bottom: 15px;
            font-family: 'Neuton', serif;
        }
        .poll-container input[type="radio"] {
            margin-right: 10px;
        }
        .poll-container button {
            background: #BC6C25;
            color: #FEFAE0;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
        }
        .poll-container button:hover {
            background: #DDA15E;
        }
        .poll-container a.back-link {
            text-decoration: none;
            color: #283618;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
            font-family: 'Neuton', serif;
        }
        .poll-container a.back-link:hover {
            color: #DDA15E;
        }
        .poll-container p {
            font-size: 18px;
            color: #283618;
            font-family: 'Neuton', serif;
        }
        /* Success and Error Messages */
        .success-message, .error-message {
            max-width: 800px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-family: 'Neuton', serif;
        }
        .success-message {
            background-color: #DFF2BF;
            color: #4F8A10;
        }
        .error-message {
            background-color: #FFBABA;
            color: #D8000C;
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
        <li><a href="polls.php">Polls</a></li>
        <li><a href="userProfile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- Poll Details -->
<div class="poll-container">
    <h1><?php echo htmlspecialchars($question, ENT_QUOTES); ?></h1>

    <?php if ($has_voted): ?>
        <p>You have already voted in this poll.</p>
        <!-- Optionally, display poll results -->
    <?php else: ?>
        <form method="POST" action="vote.php">
            <input type="hidden" name="poll_id" value="<?php echo $poll_id; ?>">
            <?php while ($option = $options_result->fetch_assoc()): ?>
            <label>
                <input type="radio" name="option_id" value="<?php echo $option['option_id']; ?>" required>
                <?php echo htmlspecialchars($option['option_text'], ENT_QUOTES); ?>
            </label>
            <?php endwhile; ?>
            <button type="submit">Vote</button>
        </form>
    <?php endif; ?>

    <a href="polls.php" class="back-link">Back to Polls</a>
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

</body>
</html>

