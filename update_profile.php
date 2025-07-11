<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    if (empty($userId) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'User ID and name are required.']);
        exit;
    }

    // Optional: Validate phone number format
    if (!empty($phone_number) && !preg_match('/^[0-9+\s-]{7,15}$/', $phone_number)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone_number = ? WHERE id = ?");
    if ($stmt->execute([$name, $phone_number, $userId])) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'name' => $name,
                'phone_number' => $phone_number
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
