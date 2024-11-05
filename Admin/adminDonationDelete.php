<?php
// adminDashboard.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';

if (isset($_GET['id'])) {
    $listing_id = intval($_GET['id']);

    // Delete donation
    $stmt = $conn->prepare('DELETE FROM food_listings WHERE listing_id = ?');
    $stmt->bind_param('i', $listing_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Donation deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to delete donation.';
    }
    $stmt->close();
}

header('Location: adminDashboard.php');
exit();
?>
