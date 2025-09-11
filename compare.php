<?php
require_once 'DBconnect.php';

// Retrieve car IDs from query parameters
if (!isset($_GET['car1'], $_GET['car2'])) {
    die('Error: Two car IDs are required for comparison.');
}

$car1_id = intval($_GET['car1']);
$car2_id = intval($_GET['car2']);

// Fetch car details from the database
$query = "SELECT listing_id, brand_name, model_name, package_name, car_condition, mileage, registration_year, production_year, price, image_path 
          FROM car_listing 
          WHERE listing_id IN (?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $car1_id, $car2_id);
$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($car = $result->fetch_assoc()) {
    $cars[] = $car;
}

if (count($cars) !== 2) {
    die('Error: Unable to fetch details for both cars.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Car Comparison</title>
    <style>
        .comparison-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .comparison-header { text-align: center; margin-bottom: 20px; }
        .package_table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .package_table th, .package_table td {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            font-size: 1.25rem;
            color: #000;
        }
        .package_table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .package_table tr:hover {
            background-color: #f0f8ff;
            transition: background-color 0.3s ease;
        }
        .package-img-container img {
            max-width: 200px;
            max-height: 200px;
            transition: transform 0.3s ease;
        }
        .package-img-container img:hover {
            transform: scale(1.1);
        }
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
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="fuelpump.php">Fuel Pumps</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main class="comparison-container">
        <div class="comparison-header">
            <h1>Car Comparison</h1>
        </div>
        <table class="package_table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th><?php echo htmlspecialchars($cars[0]['brand_name'] . ' ' . $cars[0]['model_name']); ?></th>
                    <th><?php echo htmlspecialchars($cars[1]['brand_name'] . ' ' . $cars[1]['model_name']); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Image</td>
                    <td>
                        <div class="package-img-container">
                            <img src="<?php echo htmlspecialchars($cars[0]['image_path']); ?>" alt="Car 1" />
                        </div>
                    </td>
                    <td>
                        <div class="package-img-container">
                            <img src="<?php echo htmlspecialchars($cars[1]['image_path']); ?>" alt="Car 2" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Package</td>
                    <td><?php echo htmlspecialchars($cars[0]['package_name']); ?></td>
                    <td><?php echo htmlspecialchars($cars[1]['package_name']); ?></td>
                </tr>
                <tr>
                    <td>Condition</td>
                    <td><?php echo htmlspecialchars($cars[0]['car_condition']); ?></td>
                    <td><?php echo htmlspecialchars($cars[1]['car_condition']); ?></td>
                </tr>
                <tr>
                    <td>Mileage</td>
                    <td><?php echo htmlspecialchars($cars[0]['mileage']); ?> km</td>
                    <td><?php echo htmlspecialchars($cars[1]['mileage']); ?> km</td>
                </tr>
                <tr>
                    <td>Registration Year</td>
                    <td><?php echo htmlspecialchars($cars[0]['registration_year']); ?></td>
                    <td><?php echo htmlspecialchars($cars[1]['registration_year']); ?></td>
                </tr>
                <tr>
                    <td>Production Year</td>
                    <td><?php echo htmlspecialchars($cars[0]['production_year']); ?></td>
                    <td><?php echo htmlspecialchars($cars[1]['production_year']); ?></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td>৳<?php echo htmlspecialchars($cars[0]['price']); ?></td>
                    <td>৳<?php echo htmlspecialchars($cars[1]['price']); ?></td>
                </tr>
            </tbody>
        </table>
        <p style="text-align: center; margin-top: 20px; font-size: 1.2rem; color: #555;">
            Check the Car Packages in the Car Models to see the Package details.
        </p>
    </main>
</body>
</html>
