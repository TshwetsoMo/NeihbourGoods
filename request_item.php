<?php
// request_item.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['listing_id'])) {
    $listing_id = $_GET['listing_id'];

    // Fetch the listing to ensure it exists and is available
    $stmt = $conn->prepare('SELECT * FROM food_listings WHERE listing_id = ? AND status = "available"');
    $stmt->bind_param('i', $listing_id);
    $stmt->execute();
    $listing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($listing) {
        // Insert a new request into the requests table
        $stmt = $conn->prepare('INSERT INTO requests (user_id, listing_id, request_date, status) VALUES (?, ?, NOW(), "pending")');
        $stmt->bind_param('ii', $user_id, $listing_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Your request has been submitted successfully.';
            // Optionally, update the listing status if needed
        } else {
            $_SESSION['error_message'] = 'Failed to submit your request. Please try again.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'The item you are trying to request is not available.';
    }
} else {
    $_SESSION['error_message'] = 'Invalid request.';
}

header('Location: explore.php');
exit();
?>
