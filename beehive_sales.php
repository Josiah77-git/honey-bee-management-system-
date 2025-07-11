<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customerName'] ?? '';
    $beehiveType = $_POST['beehiveType'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $pricePerUnit = $_POST['pricePerUnit'] ?? '';
    $saleDate = $_POST['saleDate'] ?? '';

    if (empty($customerName) || empty($beehiveType) || empty($quantity) || empty($pricePerUnit) || empty($saleDate)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO beehive_sales (customer_name, beehive_type, quantity, price_per_unit, sale_date) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$customerName, $beehiveType, $quantity, $pricePerUnit, $saleDate])) {
        echo json_encode(['success' => true, 'message' => 'Beehive sale recorded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record beehive sale.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
