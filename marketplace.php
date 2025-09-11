<?php
require_once('DBconnect.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['confirm'])) {
    $delete_id = intval($_GET['delete']);
    $delete_query = $conn->prepare("DELETE FROM car_listing WHERE listing_id = ? AND user_id = ?");
    $delete_query->bind_param('ii', $delete_id, $user_id);
    $delete_query->execute();
    $delete_query->close();
    header('Location: marketplace.php');
    exit();
}

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
    
    // Insert new car ad with unique seller_id
    $insert_ad = $conn->prepare("INSERT INTO car_listing (user_id, seller_id, sell_status, brand_name, model_name, package_name, car_condition, mileage, registration_year, production_year, price, image_path, all_images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_ad->bind_param('iisssssiiidss', $user_id, $unique_seller_id, $sell_status, $brand_name, $model_name, $package_name, $car_condition, $mileage, $registration_year, $production_year, $price, $main_image, $all_images);
    $insert_ad->execute();
    $insert_ad->close();
    header('Location: marketplace.php');
    exit();
}

// Check if viewing details
$view_details = isset($_GET['details']) ? intval($_GET['details']) : null;

// Fetch car ad details if viewing details
$ad_details = null;
if ($view_details) {
    $details_query = $conn->prepare("SELECT cl.*, u.name, u.email, u.phone_number FROM car_listing cl JOIN user u ON cl.user_id = u.user_id WHERE cl.listing_id = ?");
    $details_query->bind_param('i', $view_details);
    $details_query->execute();
    $result = $details_query->get_result();
    $ad_details = $result->fetch_assoc();
    $details_query->close();
}

// Fetch all car ads for listing
$ads = $conn->query("SELECT cl.listing_id, cl.brand_name, cl.model_name, cl.registration_year, cl.sell_status, cl.image_path, cl.user_id FROM car_listing cl WHERE cl.sell_status != 'seller' ORDER BY cl.listing_id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Marketplace</title>
    <style>
        .marketplace-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .marketplace-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .post-ad-btn { background: #1976d2; color: #fff; border: none; border-radius: 6px; padding: 12px 24px; font-size: 1.1rem; cursor: pointer; text-decoration: none; display: inline-block; }
        .post-ad-btn:hover { background: #1565c0; }
        .car-listing { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .car-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .car-image { width: 100%; height: 200px; object-fit: cover; border-radius: 6px; margin-bottom: 10px; background: #f5f5f5; }
        .car-info h3 { margin: 0 0 8px 0; font-size: 1.2rem; color: #333; }
        .car-info p { margin: 4px 0; color: #666; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; font-weight: bold; }
        .status-unsold { background: #e8f5e8; color: #2e7d32; }
        .status-sold { background: #ffebee; color: #c62828; }
        .details-btn { background: #4caf50; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer; margin-top: 10px; }
        .details-btn:hover { background: #45a049; }
        .delete-btn { background: #f44336; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer; margin-top: 10px; margin-left: 5px; }
        .delete-btn:hover { background: #d32f2f; }
        .btn-group { display: flex; gap: 5px; margin-top: 10px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: #fff; border-radius: 8px; padding: 30px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; }
        .detail-section { margin-bottom: 15px; }
        .detail-section h4 { margin: 0 0 8px 0; color: #333; }
        .detail-section p { margin: 4px 0; color: #666; }
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin: 15px 0; }
        .gallery-image { width: 100%; height: 120px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent; }
        .gallery-image:hover { border-color: #1976d2; }
        .main-image { width: 100%; max-height: 600px; object-fit: cover; border-radius: 6px; margin-bottom: 15px; cursor: pointer; transition: transform 0.3s ease; }
        .main-image:hover { transform: scale(1.02); }
        .compare-checkbox { margin-right: 10px; }
        .compare-btn { background: #2196f3; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer; margin-top: 10px; }
        .compare-btn:hover { background: #1976d2; }
        .comparison-modal { max-width: 800px; }
        .comparison-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .comparison-table th, .comparison-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .comparison-table th { background: #f2f2f2; }
    </style>
</head>
<body class="marketplace">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="fuelpump.php">Fuel Pumps</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="marketplace-container">
            <?php if ($ad_details): ?>
                <!-- Car Details View -->
                <div class="modal show">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><?php echo htmlspecialchars($ad_details['brand_name'] . ' ' . $ad_details['model_name']); ?></h2>
                            <a href="marketplace.php" class="close-btn">&times;</a>
                        </div>
                        
                        <?php if (isset($ad_details['image_path']) && $ad_details['image_path'] && file_exists($ad_details['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($ad_details['image_path']); ?>" alt="Main Car Image" class="main-image" id="mainImage">
                        <?php endif; ?>
                        
                        <?php if (isset($ad_details['all_images']) && $ad_details['all_images']): ?>
                            <?php 
                            $all_images = explode(',', $ad_details['all_images']);
                            if (count($all_images) > 1): ?>
                                <h4>More Images</h4>
                                <div class="image-gallery">
                                    <?php foreach ($all_images as $img_path): ?>
                                        <?php if (trim($img_path) && file_exists(trim($img_path))): ?>
                                            <img src="<?php echo htmlspecialchars(trim($img_path)); ?>" alt="Car Image" class="gallery-image" onclick="changeMainImage('<?php echo htmlspecialchars(trim($img_path)); ?>')">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="detail-section">
                            <h4>Vehicle Information</h4>
                            <p><strong>Brand:</strong> <?php echo htmlspecialchars($ad_details['brand_name']); ?></p>
                            <p><strong>Model:</strong> <?php echo htmlspecialchars($ad_details['model_name']); ?></p>
                            <p><strong>Package:</strong> <?php echo htmlspecialchars($ad_details['package_name']); ?></p>
                            <p><strong>Condition:</strong> <?php echo htmlspecialchars($ad_details['car_condition']); ?></p>
                            <p><strong>Mileage:</strong> <?php echo htmlspecialchars($ad_details['mileage']); ?> km</p>
                            <p><strong>Production Year:</strong> <?php echo htmlspecialchars($ad_details['production_year']); ?></p>
                            <p><strong>Registration Year:</strong> <?php echo htmlspecialchars($ad_details['registration_year']); ?></p>
                            <p><strong>Price:</strong> ৳<?php echo htmlspecialchars($ad_details['price']); ?></p>
                            <p><strong>Status:</strong> <span class="status-badge status-<?php echo $ad_details['sell_status']; ?>"><?php echo ucfirst($ad_details['sell_status']); ?></span></p>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Seller Contact Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($ad_details['name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($ad_details['email']); ?></p>
                            <?php if ($ad_details['phone_number']): ?>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($ad_details['phone_number']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Car Listing View -->
                <div class="marketplace-header">
                    <h1>Car Marketplace</h1>
                    <div style="display: flex; gap: 10px;">
                        <a href="post_ad.php" class="post-ad-btn">Post Sale Ad</a>
                        <button onclick="compareCars()" class="compare-btn" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Compare Cars</button>
                    </div>
                </div>
                
                <div class="car-listing">
                    <?php while ($ad = $ads->fetch_assoc()): ?>
                        <div class="car-card" data-car-id="<?php echo $ad['listing_id']; ?>">
                            <?php if ($ad['image_path'] && file_exists($ad['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" alt="Car Image" class="car-image">
                            <?php else: ?>
                                <div class="car-image" style="display: flex; align-items: center; justify-content: center; color: #999;">No Image</div>
                            <?php endif; ?>
                            
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($ad['brand_name'] . ' ' . $ad['model_name']); ?></h3>
                                <p><strong>Year:</strong> <?php echo htmlspecialchars($ad['registration_year']); ?></p>
                                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $ad['sell_status']; ?>"><?php echo ucfirst($ad['sell_status']); ?></span></p>
                                
                                <div class="btn-group">
                                    <button onclick="window.location.href='marketplace.php?details=<?php echo $ad['listing_id']; ?>'" class="details-btn">View Details</button>
                                    <?php if ($ad['user_id'] == $user_id): ?>
                                        <button onclick="if(confirm('Are you sure you want to delete this ad?')) window.location.href='marketplace.php?delete=<?php echo $ad['listing_id']; ?>&confirm=1'" class="delete-btn">Delete</button>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Comparison feature -->
                                <div style="margin-top: 10px;">
                                    <input type="checkbox" class="compare-checkbox" id="compare_<?php echo $ad['listing_id']; ?>" value="<?php echo $ad['listing_id']; ?>" onchange="toggleCarSelection('<?php echo $ad['listing_id']; ?>', this)">
                                    <label for="compare_<?php echo $ad['listing_id']; ?>" style="cursor: pointer;">Compare</label>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Comparison Modal -->
                <div class="modal" id="comparisonModal">
                    <div class="modal-content comparison-modal">
                        <div class="modal-header">
                            <h2>Compare Cars</h2>
                            <button class="close-btn" onclick="closeComparison()">&times;</button>
                        </div>
                        
                        <div class="modal-body" id="comparisonModalBody">
                            <!-- Comparison details will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

<script>
function changeMainImage(imagePath) {
    document.getElementById('mainImage').src = imagePath;
}

let selectedCars = [];

function toggleCarSelection(carId, checkbox) {
    if (checkbox.checked) {
        if (selectedCars.length >= 2) {
            alert('You can only compare two cars at a time.');
            checkbox.checked = false;
            return;
        }
        selectedCars.push(carId);
    } else {
        selectedCars = selectedCars.filter(id => id !== carId);
    }
    console.log('Selected cars:', selectedCars);
}

function compareCars() {
    if (selectedCars.length !== 2) {
        alert('Please select exactly two cars to compare.');
        return;
    }

    // Redirect to compare.php with car IDs as query parameters
    const url = `compare.php?car1=${selectedCars[0]}&car2=${selectedCars[1]}`;
    window.location.href = url;
}

function displayComparison(cars) {
    const modal = document.getElementById('comparisonModal');
    const table = document.getElementById('comparisonTable');

    let html = '<table class="comparison-table">';
    html += '<tr><th>Feature</th>';
    cars.forEach(car => {
        html += `<th>${car.brand_name} ${car.model_name}</th>`;
    });
    html += '</tr>';

    // Add rows for each feature
    const features = ['package_name', 'car_condition', 'mileage', 'registration_year', 'production_year', 'price'];
    const featureLabels = {
        package_name: 'Package',
        car_condition: 'Condition',
        mileage: 'Mileage (km)',
        registration_year: 'Registration Year',
        production_year: 'Production Year',
        price: 'Price (৳)'
    };

    features.forEach(feature => {
        html += `<tr><td><strong>${featureLabels[feature]}</strong></td>`;
        cars.forEach(car => {
            html += `<td>${car[feature]}</td>`;
        });
        html += '</tr>';
    });

    html += '</table>';
    table.innerHTML = html;
    modal.style.display = 'block';
}

function closeComparison() {
    const modal = document.getElementById('comparisonModal');
    modal.style.display = 'none';
}
</script>
</body>
</html>
