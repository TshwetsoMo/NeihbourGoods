<?php
// edit_donation.php
session_start();

// Include your database connection
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $listing_id = $_POST['listing_id'];
    $item_description = trim($_POST['item_description']);
    $quantity = trim($_POST['quantity']);
    $expiration_date = $_POST['expiration_date'] ?: null;
    $pickup_location = trim($_POST['pickup_location']);

    // Validate required fields
    if (empty($item_description) || empty($quantity) || empty($pickup_location)) {
        $_SESSION['error_message'] = 'Please fill in all required fields.';
        header('Location: dashboard.php');
        exit();
    }

    // Check if the donation belongs to the user
    $stmt = $conn->prepare('SELECT * FROM food_listings WHERE listing_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $listing_id, $user_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$donation) {
        $_SESSION['error_message'] = 'Donation not found or you do not have permission to edit this donation.';
        header('Location: dashboard.php');
        exit();
    }

    // Handle image upload if a new image is provided
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['item_image']['tmp_name'];
        $fileName = $_FILES['item_image']['name'];
        $fileSize = $_FILES['item_image']['size'];
        $fileType = $_FILES['item_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name and define allowed extensions
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/donations/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $dest_path;
                // Optionally, delete the old image file if exists
                if (!empty($donation['image_path']) && file_exists($donation['image_path'])) {
                    unlink($donation['image_path']);
                }
            } else {
                $_SESSION['error_message'] = 'There was an error uploading your image.';
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $image_path = $donation['image_path']; // Keep the existing image if no new image is uploaded
    }

    // Update the donation in the database
    $stmt = $conn->prepare('UPDATE food_listings SET item_description = ?, quantity = ?, expiration_date = ?, pickup_location = ?, image_path = ? WHERE listing_id = ?');
    $stmt->bind_param('sssssi', $item_description, $quantity, $expiration_date, $pickup_location, $image_path, $listing_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Donation updated successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to update the donation. Please try again.';
    }
    $stmt->close();

    header('Location: dashboard.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: dashboard.php');
    exit();
}
?>

