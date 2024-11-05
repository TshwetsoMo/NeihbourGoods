<?php
// adminDonationEdit.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $listing_id = intval($_GET['id']);

    // Fetch donation data
    $stmt = $conn->prepare('SELECT item_description, quantity, expiration_date, pickup_location, status FROM food_listings WHERE listing_id = ?');
    $stmt->bind_param('i', $listing_id);
    $stmt->execute();
    $stmt->bind_result($item_description, $quantity, $expiration_date, $pickup_location, $status);
    $stmt->fetch();
    $stmt->close();

    if (!$item_description) {
        $_SESSION['error_message'] = 'Donation not found.';
        header('Location: adminDashboard.php');
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update donation data
    $listing_id = intval($_POST['listing_id']);
    $item_description = trim($_POST['item_description']);
    $quantity = intval($_POST['quantity']);
    $expiration_date = $_POST['expiration_date'];
    $pickup_location = trim($_POST['pickup_location']);
    $status = trim($_POST['status']);

    // Update the database
    $stmt = $conn->prepare('UPDATE food_listings SET item_description = ?, quantity = ?, expiration_date = ?, pickup_location = ?, status = ? WHERE listing_id = ?');
    $stmt->bind_param('sisssi', $item_description, $quantity, $expiration_date, $pickup_location, $status, $listing_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Donation updated successfully.';
        header('Location: adminDashboard.php');
        exit();
    } else {
        $error = 'Failed to update donation.';
    }
    $stmt->close();
} else {
    header('Location: adminDashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Donation - NeighbourGoods Admin</title>
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

        /* Form Styles */
        .form-container {
            max-width: 800px;
            margin: 80px auto;
            background: #FEFAE0;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }

        .form-container h1 {
            text-align: center;
            color: #606C38;
            margin-bottom: 30px;
            font-family: 'Libre Baskerville', serif;
        }

        .form-group label {
            font-weight: bold;
            color: #283618;
            margin-bottom: 5px;
            font-family: 'Neuton', serif;
        }

        .form-control {
            border: 1px solid #BC6C25;
            border-radius: 5px;
            background: #fff;
            color: #283618;
            font-family: 'Neuton', serif;
        }

        .btn-custom {
            padding: 10px 20px;
            background: #BC6C25;
            color: #FEFAE0;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
            border: none;
            width: 100%;
            font-size: 18px;
        }

        .btn-custom:hover {
            background: #DDA15E;
        }

        .error {
            color: #E65C50;
            margin-bottom: 20px;
            text-align: center;
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
                <a href="adminDashboard.php" class="nav-item nav-link">Dashboard</a>
                <a href="logout.php" class="nav-item nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Form Container -->
    <div class="form-container">
        <h1>Edit Donation</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="adminDonationEdit.php" method="POST">
            <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
            <div class="mb-3 form-group">
                <label>Item Description:</label>
                <textarea name="item_description" class="form-control" rows="4" required><?php echo htmlspecialchars($item_description); ?></textarea>
            </div>
            <div class="mb-3 form-group">
                <label>Quantity:</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($quantity); ?>" required>
            </div>
            <div class="mb-3 form-group">
                <label>Expiration Date:</label>
                <input type="date" name="expiration_date" class="form-control" value="<?php echo htmlspecialchars($expiration_date); ?>">
            </div>
            <div class="mb-3 form-group">
                <label>Pickup Location:</label>
                <input type="text" name="pickup_location" class="form-control" value="<?php echo htmlspecialchars($pickup_location); ?>" required>
            </div>
            <div class="mb-3 form-group">
                <label>Status:</label>
                <select name="status" class="form-control">
                    <option value="available" <?php if ($status == 'available') echo 'selected'; ?>>Available</option>
                    <option value="unavailable" <?php if ($status == 'unavailable') echo 'selected'; ?>>Unavailable</option>
                </select>
            </div>
            <button type="submit" class="btn-custom">Save Changes</button>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

