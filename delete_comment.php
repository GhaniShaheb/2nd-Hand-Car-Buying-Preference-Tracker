<?php
require_once('DBconnect.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['comment_id'])) {
    header('Location: car_model.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = intval($_POST['comment_id']);

// Check if the comment belongs to the logged-in user and get the model_id for redirect
$check_sql = "SELECT user_id, Model_ID FROM comment WHERE comment_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param('i', $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    if ($row['user_id'] == $user_id) {
        // Delete the comment
        $delete_sql = "DELETE FROM comment WHERE comment_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param('i', $comment_id);
        $delete_stmt->execute();
        
        // Redirect back to the specific model page
        header('Location: car_model.php');
        exit();
    }
}

header('Location: car_model.php');
exit();
?>
