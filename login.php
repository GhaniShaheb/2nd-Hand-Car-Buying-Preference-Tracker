<?php
require_once('DBconnect.php');
session_start();

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $stored = $row['password'];

        // support hashed passwords (preferred) and plain fallback
        if (password_verify($password, $stored) || $password === $stored) {
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: home.php");
            exit();
        }
    }

    header("Location: index.php?error=invalid_credentials");
    exit();
} else {
    header("Location: index.php?error=missing_fields");
    exit();
}
?>