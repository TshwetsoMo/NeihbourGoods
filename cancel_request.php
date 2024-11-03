<?php
// cancel_request.php
session_start();

// Include your database connection
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Verify that the request exists and belongs to the user
    $stmt = $conn->prepare('SELECT * FROM requests WHERE request_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $request_id, $user_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($request) {
        // Delete the request from the database
        $stmt = $conn->prepare('DELETE FROM requests WHERE request_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $request_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Your request has been deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete your request. Please try again.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'Request not found or you do not have permission to delete this request.';
    }
} else {
    $_SESSION['error_message'] = 'Invalid request.';
}

header('Location: dashboard.php');
exit();
?>
