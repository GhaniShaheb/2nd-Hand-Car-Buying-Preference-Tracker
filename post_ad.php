<?php
require_once('DBconnect.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle car ad submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand_name'], $_POST['model_name'], $_POST['package_name'], $_POST['car_condition'], $_POST['mileage'], $_POST['registration_year'], $_POST['production_year'], $_POST['price'])) {
    $brand_name = $_POST['brand_name'];
    $model_name = $_POST['model_name'];
    $package_name = $_POST['package_name'];
    $car_condition = $_POST['car_condition'];
    $mileage = intval($_POST['mileage']);
    $registration_year = intval($_POST['registration_year']);
    $production_year = intval($_POST['production_year']);
    $price = floatval($_POST['price']);
    $sell_status = 'unsold';
    
    // Generate a unique seller_id based on current timestamp and user_id
    $unique_seller_id = time() + $user_id;
    
    // Handle multiple image uploads
    $image_paths = array();
    $main_image = '';
    
    if (isset($_FILES['car_images']) && count($_FILES['car_images']['name']) > 0) {
        $upload_dir = 'img/car_ads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        for ($i = 0; $i < count($_FILES['car_images']['name']) && $i < 10; $i++) {
            if ($_FILES['car_images']['error'][$i] === 0) {
                $image_name = $unique_seller_id . '_' . time() . '_' . $i . '_' . $_FILES['car_images']['name'][$i];
                $image_path = $upload_dir . $image_name;
                if (move_uploaded_file($_FILES['car_images']['tmp_name'][$i], $image_path)) {
                    $image_paths[] = $image_path;
                    if ($i === 0) $main_image = $image_path; // First image as main
                }
            }
        }
    }
    
    $all_images = implode(',', $image_paths); // Store all image paths separated by commas
    
    // Insert new car ad with unique seller_id (including all images)
    $insert_ad = $conn->prepare("INSERT INTO car_listing (user_id, seller_id, sell_status, brand_name, model_name, package_name, car_condition, mileage, registration_year, production_year, price, image_path, all_images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_ad->bind_param('iisssssiiidss', $user_id, $unique_seller_id, $sell_status, $brand_name, $model_name, $package_name, $car_condition, $mileage, $registration_year, $production_year, $price, $main_image, $all_images);
    $insert_ad->execute();
    $insert_ad->close();
    header('Location: marketplace.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Post Sale Ad</title>
    <style>
        .post-ad-container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-container { background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group textarea { padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #1976d2; }
        .image-upload { margin: 20px 0; }
        .submit-btn { background: #1976d2; color: #fff; border: none; border-radius: 6px; padding: 15px 30px; font-size: 1.1rem; cursor: pointer; width: 100%; }
        .submit-btn:hover { background: #1565c0; }
        .back-btn { background: #666; color: #fff; border: none; border-radius: 6px; padding: 10px 20px; margin-bottom: 20px; cursor: pointer; text-decoration: none; display: inline-block; }
        .back-btn:hover { background: #555; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="post-ad-container">
            <a href="marketplace.php" class="back-btn">← Back to Marketplace</a>
            
            <div class="form-container">
                <h1>Post Your Car for Sale</h1>
                
                <form action="post_ad.php" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Brand Name:</label>
                            <input type="text" name="brand_name" required>
                        </div>
                        <div class="form-group">
                            <label>Model Name:</label>
                            <input type="text" name="model_name" required>
                        </div>
                        <div class="form-group">
                            <label>Package Name:</label>
                            <input type="text" name="package_name" required>
                        </div>
                        <div class="form-group">
                            <label>Condition:</label>
                            <input type="text" name="car_condition" required>
                        </div>
                        <div class="form-group">
                            <label>Mileage (km):</label>
                            <input type="number" name="mileage" required>
                        </div>
                        <div class="form-group">
                            <label>Production Year:</label>
                            <input type="number" name="production_year" required>
                        </div>
                        <div class="form-group">
                            <label>Registration Year:</label>
                            <input type="number" name="registration_year" required>
                        </div>
                        <div class="form-group">
                            <label>Price (৳):</label>
                            <input type="number" name="price" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="image-upload">
                        <label>Upload car images (up to 10):</label>
                        <input type="file" name="car_images[]" accept="image/*" multiple onchange="if(this.files.length>10){alert('You can upload up to 10 images.');this.value='';}">
                        <small>Maximum 10 images allowed. First image will be used as the main display image.</small>
                    </div>
                    
                    <button type="submit" class="submit-btn">Post Advertisement</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
