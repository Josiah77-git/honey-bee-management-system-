<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Added OPTIONS method
header("Access-Control-Allow-Headers: Content-Type"); // Crucial for preflight requests

// Exit early for preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond with 200 OK for preflight
    exit();
}

// Database connection
$host = "localhost";
$dbname = "honeybee_db";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]); // Added error detail for debugging
    exit;
}

// Handle GET request: fetch receipts for a specific user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['user_id'])) {
        echo json_encode(["success" => false, "message" => "Missing user_id"]);
        exit;
    }

    $user_id = (int)$_GET['user_id'];
    $sql = "SELECT * FROM receipts WHERE user_id = $user_id ORDER BY sale_date DESC";
    $result = $conn->query($sql);

    $receipts = [];
    if ($result) { // Check if query was successful
        while ($row = $result->fetch_assoc()) {
            $receipts[] = $row;
        }
        echo json_encode(["success" => true, "receipts" => $receipts]);
    } else {
        echo json_encode(["success" => false, "message" => "Error fetching receipts: " . $conn->error]);
    }
    $conn->close();
    exit;
}


// Handle POST request: save a new receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Add JSON decoding error check
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["success" => false, "message" => "Invalid JSON payload: " . json_last_error_msg()]);
        exit;
    }

    if (
        !isset($data['user_id']) || // Changed empty to !isset to allow 0 or false values if intended
        !isset($data['customer_name']) ||
        !isset($data['item_type']) ||
        !isset($data['item_name']) ||
        !isset($data['quantity']) ||
        !isset($data['price']) ||
        !isset($data['sale_date'])
    ) {
        echo json_encode(["success" => false, "message" => "Missing required fields in payload"]);
        exit;
    }

    // Sanitize and assign
    $user_id = (int)$data['user_id'];
    $customer_name = $conn->real_escape_string($data['customer_name']);
    $item_type = $conn->real_escape_string($data['item_type']);
    $item_name = $conn->real_escape_string($data['item_name']);
    $quantity = (float)$data['quantity'];
    $price = (float)$data['price'];
    $total = $quantity * $price;
    $sale_date = $conn->real_escape_string($data['sale_date']);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO receipts (user_id, customer_name, item_type, item_name, quantity, price, total, sale_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) { // Check if prepare failed
        echo json_encode(["success" => false, "message" => "Prepare statement failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isssddds", $user_id, $customer_name, $item_type, $item_name, $quantity, $price, $total, $sale_date);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Receipt saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database insert failed: " . $stmt->error]); // Added stmt error for debugging
    }

    $stmt->close();
    $conn->close();
    exit;
}

// If request method is not GET, POST, or OPTIONS
echo json_encode(["success" => false, "message" => "Invalid request method"]);
exit;
?>