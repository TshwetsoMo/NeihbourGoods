<?php
// add_post.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Include your database connection
require 'config.php';

$user_id = $_SESSION['user_id'];

// Validate and sanitize inputs
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';

if (empty($title) || empty($content) || empty($category)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit();
}

// Handle media upload if exists
$media_path = NULL;
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/avi'];
    if (!in_array($_FILES['media']['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid media type.']);
        exit();
    }

    $upload_dir = 'uploads/posts/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $file_destination = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['media']['tmp_name'], $file_destination)) {
        $media_path = $file_destination;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload media.']);
        exit();
    }
}

// Insert the new post into the database
$stmt = $conn->prepare('INSERT INTO posts (user_id, title, content, media_path, category, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
$stmt->bind_param('issss', $user_id, $title, $content, $media_path, $category);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Post added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add post.']);
}

$stmt->close();
$conn->close();
?>

