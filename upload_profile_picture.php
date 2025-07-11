<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $profilePicture = $_FILES['profile_picture'] ?? null;

    if (empty($userId) || !$profilePicture) {
        echo json_encode(['success' => false, 'message' => 'User ID and profile picture are required.']);
        exit;
    }

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $imageFileType = strtolower(pathinfo($profilePicture["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    $check = getimagesize($profilePicture["tmp_name"]);
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        exit;
    }

    if (!in_array($imageFileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.']);
        exit;
    }

    if ($profilePicture["size"] > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB limit.']);
        exit;
    }

    $newFileName = uniqid('profile_', true) . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;

    if (move_uploaded_file($profilePicture["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE users SET profile_picture_url = ? WHERE id = ?");
        if ($stmt->execute([$targetFile, $userId])) {
            echo json_encode(['success' => true, 'profile_picture_url' => $targetFile]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile picture in database.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
