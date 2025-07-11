<?php
// admin_login.php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

// Basic validation
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    $conn->close();
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password']; // Raw password

// Fetch admin by email
$stmt = $conn->prepare("SELECT id, password_hash FROM administrators WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    // Verify the password
    if (password_verify($password, $admin['password_hash'])) {
        echo json_encode(['success' => true, 'message' => 'Admin login successful.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
}

$stmt->close();
$conn->close();
?>