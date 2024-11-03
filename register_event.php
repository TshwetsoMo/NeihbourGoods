<?php
// register_event.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user is already registered
    $stmt = $conn->prepare('SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $event_id, $user_id);
    $stmt->execute();
    $is_registered = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$is_registered) {
        // Register the user for the event
        $stmt = $conn->prepare('INSERT INTO event_participants (event_id, user_id, registration_date) VALUES (?, ?, NOW())');
        $stmt->bind_param('ii', $event_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'You have successfully registered for the event.';
        } else {
            $_SESSION['error_message'] = 'Failed to register for the event. Please try again.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'You are already registered for this event.';
    }
    header('Location: event_details.php?event_id=' . $event_id);
    exit();
} else {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: events.php');
    exit();
}
?>
