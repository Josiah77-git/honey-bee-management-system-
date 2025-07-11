<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hiveId = $_POST['hiveId'] ?? '';
    $location = $_POST['location'] ?? '';
    $inspectionDate = $_POST['inspectionDate'] ?? '';
    $colonyHealth = $_POST['colonyHealth'] ?? '';
    $honeyProductionEstimate = $_POST['honeyProductionEstimate'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($hiveId) || empty($location) || empty($inspectionDate) || empty($colonyHealth)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO beehive_monitoring (hive_id, location, inspection_date, colony_health, honey_production_estimate, notes) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$hiveId, $location, $inspectionDate, $colonyHealth, $honeyProductionEstimate, $notes])) {
        echo json_encode(['success' => true, 'message' => 'Beehive monitoring recorded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record beehive monitoring.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
