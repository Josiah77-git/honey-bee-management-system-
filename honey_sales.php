<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customerName'] ?? '';
    $honeyType = $_POST['honeyType'] ?? '';
    $quantityKg = $_POST['quantityKg'] ?? '';
    $pricePerKg = $_POST['pricePerKg'] ?? '';
    $saleDate = $_POST['saleDate'] ?? '';

    if (empty($customerName) || empty($honeyType) || empty($quantityKg) || empty($pricePerKg) || empty($saleDate)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO honey_sales (customer_name, honey_type, quantity_kg, price_per_kg, sale_date) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$customerName, $honeyType, $quantityKg, $pricePerKg, $saleDate])) {
        echo json_encode(['success' => true, 'message' => 'Honey sale recorded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record honey sale.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
