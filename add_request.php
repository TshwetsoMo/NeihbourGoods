<?php
// add_request.php
session_start();

// Include your database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_description = trim($_POST['item_description']);
    $quantity = trim($_POST['quantity']);
    $image_path = null;

    // Handle image upload if any
    if (isset($_FILES['request_image']) && $_FILES['request_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['request_image']['tmp_name'];
        $fileName = $_FILES['request_image']['name'];
        $fileSize = $_FILES['request_image']['size'];
        $fileType = $_FILES['request_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name and define allowed extensions
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/requests/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $dest_path;
            } else {
                $_SESSION['error_message'] = 'There was an error uploading your image.';
            }
        } else {
            $_SESSION['error_message'] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    if (empty($_SESSION['error_message'])) {
        // Insert the new request into the database
        $stmt = $conn->prepare('INSERT INTO requests (user_id, item_description, quantity, image_path, request_date, status) VALUES (?, ?, ?, ?, NOW(), "open")');
        $stmt->bind_param('isss', $user_id, $item_description, $quantity, $image_path);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Your request has been posted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to post your request. Please try again.';
        }
        $stmt->close();
    }
    header('Location: explore.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: explore.php');
    exit();
}
?>
