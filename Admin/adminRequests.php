<?php
// adminRequests.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Fetch all requests
$stmt = $conn->prepare('SELECT r.request_id, r.status, r.request_date, u.name AS requester_name, f.item_description FROM requests r JOIN users u ON r.user_id = u.user_id JOIN food_listings f ON r.listing_id = f.listing_id');
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Management - NeighbourGoods Admin</title>
    <!-- Include your CSS and other headers -->
</head>
<body>
    <!-- Include your admin navbar here -->
    <h1>Request Management</h1>
    <table>
        <thead>
            <tr>
                <th>Requester</th>
                <th>Item Requested</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($request = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                <td><?php echo htmlspecialchars($request['item_description']); ?></td>
                <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td>
                    <a href="adminRequestEdit.php?id=<?php echo $request['request_id']; ?>">Edit</a>
                    <a href="adminRequestDelete.php?id=<?php echo $request['request_id']; ?>" onclick="return confirm('Are you sure you want to delete this request?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
