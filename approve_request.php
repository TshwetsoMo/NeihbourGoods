<?php
// approve_request.php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$request_id = $_GET['request_id'];
$user_id = $_SESSION['user_id'];

// Update the request status to 'approved'
$stmt = $conn->prepare('UPDATE requests r JOIN food_listings f ON r.listing_id = f.listing_id SET r.status = "approved" WHERE r.request_id = ? AND f.user_id = ?');
$stmt->bind_param('ii', $request_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = 'Request approved successfully.';
} else {
    $_SESSION['error_message'] = 'Failed to approve request.';
}
$stmt->close();

header('Location: dashboard.php');
exit();
?>
