<?php
session_start();
require_once 'DBconnect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Debugging: Log the raw JSON input
$rawInput = file_get_contents('php://input');
error_log("Raw JSON input: $rawInput");

$input = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

if (!isset($input['car_ids']) || !is_array($input['car_ids']) || count($input['car_ids']) !== 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$car_ids = array_map('intval', $input['car_ids']);
$placeholders = str_repeat('?,', count($car_ids) - 1) . '?';

// Fetch car data
$query = "SELECT listing_id, brand_name, model_name, package_name, car_condition, mileage, registration_year, production_year, price, image_path, all_images 
          FROM car_listing 
          WHERE listing_id IN ($placeholders)
          ORDER BY FIELD(listing_id, " . implode(',', $car_ids) . ")";

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('i', count($car_ids)), ...$car_ids);

// Debugging: Log the received car IDs and query execution status
error_log("Received car IDs: " . implode(',', $car_ids));
error_log("Query: $query");
if (!$stmt) {
    error_log("Query preparation failed: " . $conn->error);
}
if (!$stmt->execute()) {
    error_log("Query execution failed: " . $stmt->error);
}

$result = $stmt->get_result();

$cars = [];
while ($car = $result->fetch_assoc()) {
    $cars[] = $car;
}

header('Content-Type: application/json');
echo json_encode($cars);
?>
