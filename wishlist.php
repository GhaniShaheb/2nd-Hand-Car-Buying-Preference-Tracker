<?php
require_once('DBconnect.php');
session_start();

// Fetch all wishlist items with full car details
$sql = "SELECT w.wishlist_id, w.date_added, c.* FROM wishlist w JOIN car_model c ON w.Model_ID = c.Model_ID ORDER BY w.date_added DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/car_models.css" />
    <title>My Wishlist</title>
</head>
<body class="car_models">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="fuelpump.php">Fuel Pumps</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="wishlist">
            <div class="carmodel_box">
                <h1>My Wishlist</h1>
                <table class="carmodel_table">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Body</th>
                            <th>Picture</th>
                            <th>Engine</th>
                            <th>Engine Capacity</th>
                            <th>Fuel Type</th>
                            <th>Seating Capacity</th>
                            <th>Production Year</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['Brand']; ?></td>
                            <td><?php echo $row['Model']; ?></td>
                            <td><?php echo $row['Body']; ?></td>
                            <td>
                                <?php 
                                $model_id = $row['Model_ID'];
                                $main_img = "img/cars/img_cars_{$model_id}_1.jpg";
                                $hover_img = "img/cars/img_cars_{$model_id}_2.jpg";
                                ?>
                                <div class="car-img-container">
                                    <img src="<?php echo $main_img; ?>" class="car-img" onmouseover="this.src='<?php echo $hover_img; ?>'" onmouseout="this.src='<?php echo $main_img; ?>'" alt="Car Model" />
                                </div>
                            </td>
                            <td><?php echo $row['Engine']; ?></td>
                            <td><?php echo $row['Engine_Capacity']; ?></td>
                            <td><?php echo $row['Fuel_Type']; ?></td>
                            <td><?php echo $row['Seating_Capacity']; ?></td>
                            <td><?php echo $row['Production_Year']; ?></td>
                            <td><?php echo $row['date_added']; ?>
                                <form action="remove_from_wishlist.php" method="post" style="display:inline; margin-left:10px;">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $row['wishlist_id']; ?>">
                                    <button type="submit" style="background:#d32f2f; color:#fff; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
