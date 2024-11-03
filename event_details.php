<?php
// event_details.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details
    $stmt = $conn->prepare('SELECT * FROM events WHERE event_id = ?');
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$event) {
        $_SESSION['error_message'] = 'Event not found.';
        header('Location: events.php');
        exit();
    }

    // Check if user is already registered
    $stmt = $conn->prepare('SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $event_id, $_SESSION['user_id']);
    $stmt->execute();
    $is_registered = $stmt->get_result()->num_rows > 0;
    $stmt->close();
} else {
    $_SESSION['error_message'] = 'Invalid event.';
    header('Location: events.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details - NeighbourGoods</title>
    <!-- Include your styles and scripts -->
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <!-- Your navbar content -->
</nav>

<!-- Event Details -->
<div class="event-container">
    <h1><?php echo htmlspecialchars($event['title'], ENT_QUOTES); ?></h1>
    <p><?php echo date('F j, Y, g:i a', strtotime($event['event_date'])); ?></p>
    <p>Location: <?php echo htmlspecialchars($event['location'], ENT_QUOTES); ?></p>
    <p><?php echo nl2br(htmlspecialchars($event['description'], ENT_QUOTES)); ?></p>

    <?php if ($is_registered): ?>
        <p>You are registered for this event.</p>
    <?php else: ?>
        <form action="register_event.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
            <button type="submit">Register for Event</button>
        </form>
    <?php endif; ?>

    <a href="events.php">Back to Events</a>
</div>

</body>
</html>
