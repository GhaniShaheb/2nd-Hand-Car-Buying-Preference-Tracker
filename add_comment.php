<?php
require_once('DBconnect.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['model_id']) || !isset($_POST['comment'])) {
    header('Location: car_model.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$Model_ID = intval($_POST['model_id']);
$comment = trim($_POST['comment']);
$date_added = date('Y-m-d H:i:s');

// Insert comment into 'comment' table
$insert_sql = "INSERT INTO comment (Model_ID, comment_text, user_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param('isi', $Model_ID, $comment, $user_id);
$stmt->execute();

header('Location: car_model.php');
exit();
