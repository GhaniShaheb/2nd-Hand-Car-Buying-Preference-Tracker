<?php
session_start();
require_once("DBconnect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/car_models.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet" />
    <title>2nd Hand Car Advisor</title>
</head>
<body class="car_models">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="fuelpump.php">Fuel Pumps</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="Car Models">
            <div class="carmodel_box">
                <h1>Car Models</h1>
                <table class="carmodel_table">
                    <thead>
                        <tr>
                            <!-- <th>Model_ID</th> -->
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Body</th>
                            <th>Picture</th>
                            <th>Engine</th>
                            <th>Engine_Capacity</th>
                            <th>Fuel_Type</th>
                            <th>Seating_Capacity</th>
                            <th>Production_Year</th>
                            <th>Packages Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * FROM car_model";
                        $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_array($result)){
                        ?>
                        <tr>
                            <!-- <td><?php echo $row["Model_ID"]; ?></td> -->
                            <td><?php echo $row["Brand"]; ?></td>
                            <td><?php echo $row["Model"]; ?></td>
                            <td><?php echo $row["Body"]; ?></td>
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
                            <td><?php echo $row["Engine"]; ?></td>
                            <td><?php echo $row["Engine_Capacity"]; ?></td>
                            <td><?php echo $row["Fuel_Type"]; ?></td>
                            <td><?php echo $row["Seating_Capacity"]; ?></td>
                            <td><?php echo $row["Production_Year"]; ?></td>
                            <td>
                                <a href="car_package.php?model_id=<?php echo $row['Model_ID']; ?>">View Packages</a>
                                <form action="add_to_wishlist.php" method="post" style="display:inline;">
                                    <input type="hidden" name="model_id" value="<?php echo $row['Model_ID']; ?>">
                                    <button type="submit" class="wishlist-btn">Add to Wishlist</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" style="background:#f4f8fc; border-bottom:1px solid #e0e0e0;">
                                <div style="padding:10px 0;">
                                    <strong>Comments:</strong>
                                    <form action="add_comment.php" method="post" style="display:flex; gap:10px; align-items:center; margin-bottom:10px;">
                                        <input type="hidden" name="model_id" value="<?php echo $row['Model_ID']; ?>">
                                        <input type="text" name="comment" placeholder="Add a comment..." style="flex:1; padding:6px 10px; border-radius:4px; border:1px solid #ccc;" required>
                                        <button type="submit" class="comment-btn" style="padding:6px 16px; border-radius:4px; background:#1976d2; color:#fff; border:none; cursor:pointer;">Comment</button>
                                    </form>
                                    <div style="margin-top:5px;">
                                        <?php
                                        $comment_sql = "SELECT comment_id, comment_text, user_id FROM comment WHERE Model_ID = " . intval($row['Model_ID']) . " ORDER BY comment_id DESC";
                                        $comment_result = $conn->query($comment_sql);
                                        
                                        if ($comment_result && $comment_result->num_rows > 0) {
                                            while ($c = $comment_result->fetch_assoc()) {
                                                $uid = intval($c['user_id']);
                                                $comment_id = intval($c['comment_id']);
                                                $user_sql = "SELECT name FROM user WHERE user_id = $uid";
                                                $user_result = $conn->query($user_sql);
                                                $username = ($user_result && $user_result->num_rows > 0) ? htmlspecialchars($user_result->fetch_assoc()['name']) : "User $uid";
                                                $profile_pic = file_exists("img/profile_$uid.png") ? "img/profile_$uid.png" : "img/default_profile.png";
                                                
                                                echo "<div style='display:flex;align-items:center;margin-bottom:8px;padding:4px 0;border-bottom:1px solid #eee;'>";
                                                echo "<img src='$profile_pic' style='width:32px;height:32px;border-radius:50%;margin-right:10px;border:1px solid #ccc;'>";
                                                echo "<div style='color:#000;flex:1;'><b>" . $username . "</b><br>" . htmlspecialchars($c['comment_text']) . "</div>";
                                                
                                                // Show delete button only if it's the user's own comment
                                                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $uid) {
                                                    echo "<form action='delete_comment.php' method='post' style='margin-left:10px; display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this comment?\")'>";
                                                    echo "<input type='hidden' name='comment_id' value='$comment_id'>";
                                                    echo "<button type='submit' style='background:#d32f2f;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:12px;'>Delete</button>";
                                                    echo "</form>";
                                                }
                                                
                                                echo "</div>";
                                            }
                                        } else {
                                            echo "<em>No comments yet. Be the first to comment!</em>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    </body>
</html>
