<?php
require_once('DBconnect.php');

if (!isset($_POST['wishlist_id'])) {
    header('Location: wishlist.php');
    exit();
}

$wishlist_id = intval($_POST['wishlist_id']);
$sql = "DELETE FROM wishlist WHERE wishlist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $wishlist_id);
$stmt->execute();

header('Location: wishlist.php');
exit();
