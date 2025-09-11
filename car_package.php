<?php
require_once("DBconnect.php");

if (isset($_GET['model_id'])) {
    $model_id = mysqli_real_escape_string($conn, $_GET['model_id']);

    $sql = "SELECT * FROM car_packages WHERE Model_ID = '$model_id'";
    $result = mysqli_query($conn, $sql);

    $model_sql = "SELECT * FROM car_model WHERE Model_ID = '$model_id'";
    $model_result = mysqli_query($conn, $model_sql);
    $model = mysqli_fetch_assoc($model_result);
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/carpackage.css" />
    <title>Car Packages</title>
</head>
<body class="carpackage" style="font-size:1.25rem;">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
        </nav>
    </header>
    <main>
        <section class="car_packages">
            <div class="package_box" style="padding:60px 120px; max-width: 1800px; width: 98%; margin: 0 auto;">
                <h1 style="font-size:2.2rem; margin-bottom:32px;">Packages available for <?php echo $model['Brand'] . " " . $model['Model']; ?></h1>
                <table class="package_table" style="font-family: 'Fredoka', Arial, sans-serif; font-size: 1.25rem;">
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Features</th>
                            <th>Price Range</th>
                            <th>Picture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Images are named as: img/packages/package_ModelID_PackageName.jpg or .png
                                $original_name = $row['Package_Name'];
                                $package_name = strtolower($original_name);
                                $package_name = preg_replace('/[^a-z0-9]+/', '_', $package_name); // Replace any non-alphanumeric characters with underscore
                                $package_name = trim($package_name, '_'); // Remove leading/trailing underscores
                                
                                // Check for both jpg and png
                                $package_img_jpg = "img/packages/package_{$model_id}_{$package_name}.jpg";
                                $package_img_png = "img/packages/package_{$model_id}_{$package_name}.png";
                                
                                // Determine which image exists
                                if (file_exists($package_img_jpg)) {
                                    $package_img = $package_img_jpg;
                                    $file_type = "JPG";
                                } elseif (file_exists($package_img_png)) {
                                    $package_img = $package_img_png;
                                    $file_type = "PNG";
                                } else {
                                    $package_img = "img/packages/default_package.jpg";
                                    $file_type = "Default";
                                }
                        ?>
                        <tr>
                            <td style="color:#000; padding:28px 24px; font-size:1.25rem; min-width:180px;">
                                <?php echo $row['Package_Name']; ?>
                            </td>
                            <td style="color:#000; padding:28px 24px; font-size:1.25rem; min-width:260px;"><?php echo $row['Features']; ?></td>
                            <td style="color:#000; padding:28px 24px; font-size:1.25rem; min-width:140px;"><?php echo $row['Price_Range']; ?></td>
                            <td style="padding:28px 24px; min-width:160px;">
                                <div class="package-img-container" style="width: 400px; height: 600px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <img src="<?php echo $package_img; ?>" class="package-img" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';" alt="Package Picture" onerror="this.src='img/packages/default_package.jpg';" />
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'>No packages available for this model.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
