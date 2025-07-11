<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmerName = $_POST['farmerName'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $trainingTopic = $_POST['trainingTopic'] ?? '';
    $trainingDate = $_POST['trainingDate'] ?? '';
    $county = $_POST['county'] ?? '';
    $constituency = $_POST['constituency'] ?? '';
    $ward = $_POST['ward'] ?? '';

    if (empty($farmerName) || empty($contact) || empty($trainingTopic) || empty($trainingDate) || empty($county) || empty($constituency) || empty($ward)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO farmer_training (farmer_name, contact, training_topic, training_date, county, constituency, ward) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$farmerName, $contact, $trainingTopic, $trainingDate, $county, $constituency, $ward])) {
        echo json_encode(['success' => true, 'message' => 'Farmer training recorded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record farmer training.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
