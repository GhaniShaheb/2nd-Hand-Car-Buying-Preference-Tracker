<?php
require_once('DBconnect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}


$user_id = $_SESSION['user_id'];

// Handle profile picture upload
$profilePicPath = "img/profile_$user_id.png";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['profile_picture']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed)) {
        $profilePicPath = "img/profile_$user_id.$ext";
        move_uploaded_file($tmp_name, $profilePicPath);
    }
}

// Find existing profile picture
$picToShow = file_exists($profilePicPath) ? $profilePicPath : "img/default_profile.png";

// Fetch user details
$sql = "SELECT user_id, name, email, phone_number FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "SQL Error: " . $conn->error;
    exit();
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $user_name = htmlspecialchars($row['name']);
    $email = htmlspecialchars($row['email']);
    $phone_number = htmlspecialchars($row['phone_number']);
} else {
    echo 'User not found.';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/profile.css" />
    <link rel="stylesheet" href="css/profile.css" />
    <title>User Profile</title>
</head>
<body class="profile">
    <header>
        <nav>
            <div class="nav_logo">
            <h1>2nd Hand Car Advisor</h1>
            </div>
            <ul class="nav_link">
            <li><a href="home.php">Home</a></li>
            <li><a href="car_model.php">Car Models</a></li>
            <li><a href="wishlist.php">Wishlist</a></li>
            <li><a href="workshop.php">Workshops</a></li>
            <li><a href="fuelpump.php">Fuel Pumps</a></li>
            <li><a href="marketplace.php">Marketplace</a></li>

            </ul>
        </nav>
    </header>
    <main>
        <section class="profile">
            <div class="profile_box">
                <h1>User Profile</h1>
                <form class="profile_form" method="post" enctype="multipart/form-data">
                    <div class="profile-picture-section">
                        <label for="profile_picture" class="profile-picture-label">
                            <img src="<?php echo $picToShow; ?>" alt="Profile Picture" class="profile-picture" id="profilePicPreview">
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display:none;" onchange="previewProfilePic(event)">
                        </label>
                        <p class="profile-picture-text">Click to upload/change profile picture</p>
                        <button type="submit" class="profile-picture-upload-btn">Upload</button>
                    </div>
                    <div class="profile-fields">
                        <label>User ID</label>
                        <input type="text" value="<?php echo $user_id; ?>" readonly>
                        <label>Name</label>
                        <input type="text" value="<?php echo $user_name; ?>" readonly>
                        <label>Email</label>
                        <input type="text" value="<?php echo $email; ?>" readonly>
                        <label>Phone Number</label>
                        <input type="text" value="<?php echo $phone_number; ?>" readonly>
                    </div>
                </form>
                <form action="logout.php" method="post" style="margin-top:20px;">
                    <button type="submit" style="padding:8px 24px; background:#d32f2f; color:#fff; border:none; border-radius:4px; cursor:pointer;">Logout</button>
                </form>
            </div>
            <script>
            function previewProfilePic(event) {
                const reader = new FileReader();
                reader.onload = function(){
                    document.getElementById('profilePicPreview').src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
            </script>
        </section>
    </main>
</body>
</html>
