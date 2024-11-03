<?php
// get_donation_details.php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$listing_id = $_GET['listing_id'];

// Fetch donation details
$stmt = $conn->prepare('SELECT * FROM food_listings WHERE listing_id = ? AND user_id = ?');
$stmt->bind_param('ii', $listing_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($donation = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'donation' => $donation]);
} else {
    echo json_encode(['success' => false, 'message' => 'Donation not found or access denied.']);
}
$stmt->close();
$conn->close();
?>
