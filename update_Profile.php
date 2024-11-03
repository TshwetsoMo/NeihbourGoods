<?php
// update_profile.php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$address = $_POST['address'];
$phone_number = $_POST['phone_number'];
$bio = $_POST['bio'];
$availability = $_POST['availability'];
$needs = $_POST['needs'];
$password = $_POST['password'];

// Handle profile picture upload
if (!empty($_FILES['profile_picture']['name'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $uploadOk = 0;
    }

    // Upload file
    if ($uploadOk && move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        $profile_picture = $target_file;
    } else {
        $_SESSION['error_message'] = 'Profile picture upload failed.';
        header('Location: userProfile.php');
        exit();
    }
}

// Update user information
$query = "UPDATE users SET name = ?, email = ?, address = ?, phone_number = ?, bio = ?, availability = ?, needs = ?";
$params = [$name, $email, $address, $phone_number, $bio, $availability, $needs];
$types = 'sssssss';

if (isset($profile_picture)) {
    $query .= ", profile_picture = ?";
    $params[] = $profile_picture;
    $types .= 's';
}

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password = ?";
    $params[] = $hashed_password;
    $types .= 's';
}

$query .= " WHERE user_id = ?";
$params[] = $user_id;
$types .= 'i';

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'Profile updated successfully.';
} else {
    $_SESSION['error_message'] = 'Failed to update profile. Please try again.';
}

$stmt->close();
header('Location: userProfile.php');
exit();
?>
