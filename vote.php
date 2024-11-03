<?php
// vote.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poll_id'], $_POST['option_id'])) {
    $poll_id = $_POST['poll_id'];
    $option_id = $_POST['option_id'];
    $user_id = $_SESSION['user_id'];

    // Check if poll is active
    $stmt = $conn->prepare('SELECT * FROM polls WHERE poll_id = ? AND expires_at > NOW()');
    $stmt->bind_param('i', $poll_id);
    $stmt->execute();
    $poll_exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$poll_exists) {
        $_SESSION['error_message'] = 'Poll not found or has expired.';
        header('Location: polls.php');
        exit();
    }

    // Check if user has already voted
    $stmt = $conn->prepare('SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $poll_id, $user_id);
    $stmt->execute();
    $has_voted = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$has_voted) {
        // Record the vote without specifying vote_date
        $stmt = $conn->prepare('INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES (?, ?, ?)');
        $stmt->bind_param('iii', $poll_id, $option_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Your vote has been recorded.';
        } else {
            $_SESSION['error_message'] = 'Failed to record your vote. Please try again.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'You have already voted in this poll.';
    }
    
    header('Location: poll_details.php?poll_id=' . $poll_id);
    exit();
} else {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: polls.php');
    exit();
}
?>
