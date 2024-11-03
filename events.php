<?php
// events.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch upcoming events
$stmt = $conn->prepare('SELECT event_id, title, description, event_date, location FROM events WHERE event_date >= NOW() ORDER BY event_date ASC');
$stmt->execute();
$events_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events - NeighbourGoods</title>
    <!-- Include your styles and scripts -->
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <!-- Your navbar content -->
</nav>

<!-- Events List -->
<div class="events-container">
    <h1>Upcoming Events</h1>
    <?php while ($event = $events_result->fetch_assoc()): ?>
    <div class="event-item">
        <h2><?php echo htmlspecialchars($event['title'], ENT_QUOTES); ?></h2>
        <p><?php echo date('F j, Y, g:i a', strtotime($event['event_date'])); ?></p>
        <p>Location: <?php echo htmlspecialchars($event['location'], ENT_QUOTES); ?></p>
        <p><?php echo nl2br(htmlspecialchars($event['description'], ENT_QUOTES)); ?></p>
        <a href="event_details.php?event_id=<?php echo $event['event_id']; ?>">View Details</a>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
