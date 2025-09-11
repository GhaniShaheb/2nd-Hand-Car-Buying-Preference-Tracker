<?php
require_once('DBconnect.php');
session_start();

if (!isset($_POST['model_id'])) {
    header('Location: car_model.php');
    exit();
}

$Model_ID = intval($_POST['model_id']);
$date_added = date('Y-m-d H:i:s');

// Create wishlist table if not exists
$create_sql = "CREATE TABLE IF NOT EXISTS wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    Model_ID INT NOT NULL,
    date_added DATETIME NOT NULL,
    FOREIGN KEY (Model_ID) REFERENCES car_model(Model_ID)
)";
$conn->query($create_sql);

// Insert into wishlist
$insert_sql = "INSERT INTO wishlist (Model_ID, date_added) VALUES (?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param('is', $Model_ID, $date_added);
$stmt->execute();

header('Location: wishlist.php');
exit();
