<?php
session_start();
$profilePicPath = "img/default_profile.png";
$user_name = "Guest";
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $possibleExts = ['png', 'jpg', 'jpeg', 'gif'];
  foreach ($possibleExts as $ext) {
    $tryPath = "img/profile_{$user_id}.{$ext}";
    if (file_exists($tryPath)) {
      $profilePicPath = $tryPath;
      break;
    }
  }
  // Fetch username from DB
  require_once('DBconnect.php');
  $sql = "SELECT name FROM user WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
      $user_name = htmlspecialchars($row['name']);
    }
  }
}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/home.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
      rel="stylesheet"
    />
    <title>2nd Hand Car Advisor</title>
  </head>
  <body class="home">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="fuelpump.php">Fuel Pumps</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
        <a href="profile.php" class="profile-top-icon">
            <img src="<?php echo $profilePicPath; ?>" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #2a5d9f;position:absolute;top:20px;right:30px;box-shadow:0 2px 8px rgba(42,93,159,0.12);cursor:pointer;" />
        </a>
    </header>
    <main>
      <section class="home">
          <h1 style="color: white; font-weight:400;">Welcome <?php echo $user_name; ?> to 2nd Hand Car Advisor</h1>
          <div class="home-details" style="margin-top:32px;background:rgba(86, 83, 83, 0.85);border-radius:12px;padding:24px;max-width:600px;margin-left:auto;margin-right:auto;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
            <h2 style="color:#2a5d9f;">Explore the Website</h2>
            <ul style="text-align:left;font-size:1.1rem;line-height:2;">
              <li><strong>Car Models:</strong> Browse available car models with details and pictures.</li>
              <li><strong>Wishlist:</strong> Save your favorite cars for later viewing.</li>
              <li><strong>Workshops:</strong> Find trusted workshops for car maintenance and repairs.</li>
              <li><strong>Fuel Pumps:</strong> Locate authentic fuel pumps nearby.</li>
              <li><strong>Marketplace:</strong> Discover cars and packages available for purchase.</li>
              <li><strong>Profile:</strong> View and edit your personal information and profile picture.</li>
            </ul>
          </div>
      </section>
    </main>
  </body>
</html>
