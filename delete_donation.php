<?php
// delete_donation.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Use 'listing_id' to match the parameter from 'dashboard.php'
if (isset($_GET['listing_id'])) {
    // Sanitize the listing_id
    $listing_id = intval($_GET['listing_id']);

    // Verify that the donation belongs to the user
    $stmt = $conn->prepare('SELECT * FROM food_listings WHERE listing_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $listing_id, $user_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($donation) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Delete associated requests first (if any)
            $stmt = $conn->prepare('DELETE FROM requests WHERE listing_id = ?');
            $stmt->bind_param('i', $listing_id);
            $stmt->execute();
            $stmt->close();

            // Delete the donation
            $stmt = $conn->prepare('DELETE FROM food_listings WHERE listing_id = ?');
            $stmt->bind_param('i', $listing_id);
            $stmt->execute();
            $stmt->close();

            // Delete the image file if exists
            if (!empty($donation['image_path']) && file_exists($donation['image_path'])) {
                unlink($donation['image_path']);
            }

            // Commit transaction
            $conn->commit();

            $_SESSION['success_message'] = 'Donation deleted successfully!';
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $_SESSION['error_message'] = 'Failed to delete donation. Please try again.';
        }
    } else {
        $_SESSION['error_message'] = 'Donation not found or access denied.';
    }
    header('Location: dashboard.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: dashboard.php');
    exit();
}
?>
