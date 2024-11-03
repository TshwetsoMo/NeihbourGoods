<?php
// polls.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch active polls
$stmt = $conn->prepare('SELECT poll_id, question FROM polls WHERE expires_at > NOW() ORDER BY created_at DESC');
$stmt->execute();
$polls_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Polls - NeighbourGoods</title>
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
        /* Polls Container */
        .polls-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
            background: rgba(254, 250, 224, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .polls-container h1 {
            font-size: 32px;
            color: #606C38;
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Libre Baskerville', serif;
        }
        .poll-item {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            border: 1px solid #DDA15E;
        }
        .poll-item h2 {
            font-size: 24px;
            color: #283618;
            margin-bottom: 15px;
            font-family: 'Libre Baskerville', serif;
        }
        .poll-item a {
            text-decoration: none;
            background: #BC6C25;
            color: #FEFAE0;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
        }
        .poll-item a:hover {
            background: #DDA15E;
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

<!-- Polls List -->
<div class="polls-container">
    <h1>Active Polls</h1>
    <?php while ($poll = $polls_result->fetch_assoc()): ?>
    <div class="poll-item">
        <h2><?php echo htmlspecialchars($poll['question'], ENT_QUOTES); ?></h2>
        <a href="poll_details.php?poll_id=<?php echo $poll['poll_id']; ?>">Vote Now</a>
    </div>
    <?php endwhile; ?>
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
