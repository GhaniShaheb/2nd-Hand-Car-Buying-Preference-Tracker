<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="css/car_models.css" />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet" />
    <title>2nd Hand Car Advisor</title>
</head>
        <?php
        session_start();
        require_once('DBconnect.php');
        
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
<body class="fuelpumps">
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="workshop.php">Workshops</a></li>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
        <a href="profile.php" class="profile-top-icon">
            <?php if (isset($user_name) && $user_name !== "Guest"): ?>
                <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #2a5d9f;position:absolute;top:20px;right:30px;box-shadow:0 2px 8px rgba(42,93,159,0.12);cursor:pointer;" />
            <?php endif; ?>
        </a>
    </header>
    <main>
        <section class="Fuel Pumps">
            <div class="carmodel_box">
                <h1>Fuel Pumps</h1>
                <table class="carmodel_table">
                    <thead>
                        <tr>
                            <th>Fuel Pump Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Get fuel pump data with direction
                        $sql = "SELECT * FROM fuel_pump";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            $fuelpump_data = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Get fuel types for this pump
                                $fuel_types = '';
                                $fuel_sql = "SELECT GROUP_CONCAT(Fuel_Type SEPARATOR ', ') AS Fuel_Types FROM fuel_type WHERE Pump_ID = " . intval($row['Pump_ID']);
                                $fuel_result = mysqli_query($conn, $fuel_sql);
                                if ($fuel_result && mysqli_num_rows($fuel_result) > 0) {
                                    $fuel_row = mysqli_fetch_assoc($fuel_result);
                                    $fuel_types = $fuel_row['Fuel_Types'] ?? '';
                                }
                                
                                // Check for direction column
                                $direction = isset($row['direction']) ? $row['direction'] : '';
                                
                                $fuelpump_data[] = [
                                    'name' => $row['Pump_Name'],
                                    'location' => $row['Pump_Location'],
                                    'contact' => $row['Pump_Contact'],
                                    'fuel_types' => $fuel_types,
                                    'direction' => $direction
                                ];
                                
                                echo '<tr>';
                                echo '<td>';
                                echo '<a href="#" class="fuelpump-link" onclick="showFuelPumpDetails(' . (count($fuelpump_data) - 1) . '); return false;">' . 
                                     htmlspecialchars($row['Pump_Name']) . '</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td>No fuel pumps found.</td></tr>';
                            $fuelpump_data = [];
                        }
                        ?>
                        <script>
                        const fuelpumpData = <?php echo json_encode($fuelpump_data); ?>;
                        console.log('All fuel pump data with directions:', fuelpumpData);
                        </script>
                    </tbody>
                </table>
            </div>

            <!-- Fuel Pump Details Section -->
            <div id="fuelpump-details" class="carmodel_box" style="display: none; margin-top: 20px;">
                <h2>Fuel Pump Details</h2>
                <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 10px; color: #333;">
                    <p><strong>Name:</strong> <span id="detail-name"></span></p>
                    <p><strong>Location:</strong> <span id="detail-location"></span></p>
                    <p><strong>Contact:</strong> <span id="detail-contact"></span></p>
                    <p><strong>Fuel Types:</strong> <span id="detail-fuel-types"></span></p>
                    <div id="detail-map" style="margin-top: 15px;"></div>
                    <button onclick="hideFuelPumpDetails()" style="margin-top: 15px; padding: 10px 20px; background: #2a5d9f; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                </div>
            </div>
        </section>

        <script>
        function showFuelPumpDetails(index) {
            if (fuelpumpData && fuelpumpData[index]) {
                const fuelpump = fuelpumpData[index];
                console.log('Selected fuel pump:', fuelpump);
                console.log('Direction field value:', fuelpump.direction);
                
                document.getElementById('detail-name').textContent = fuelpump.name;
                document.getElementById('detail-location').textContent = fuelpump.location;
                document.getElementById('detail-contact').textContent = fuelpump.contact;
                document.getElementById('detail-fuel-types').textContent = fuelpump.fuel_types || 'Not specified';
                
                const mapDiv = document.getElementById('detail-map');
                
                if (fuelpump.direction === null || fuelpump.direction === undefined || fuelpump.direction.trim() === '') {
                    mapDiv.innerHTML = '<p>No map available for this fuel pump.</p>';
                } else {
                    // Check if it's a complete iframe HTML or just a URL
                    let directionContent = fuelpump.direction.trim();
                    
                    if (directionContent.includes('<iframe')) {
                        // It's complete iframe HTML, use it directly
                        console.log('Using complete iframe HTML');
                        mapDiv.innerHTML = directionContent;
                    } else if (directionContent.startsWith('http://') || directionContent.startsWith('https://')) {
                        // It's just a URL, create iframe
                        console.log('Creating iframe from URL');
                        mapDiv.innerHTML = '<iframe src="' + directionContent + '" width="100%" height="300" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                    } else {
                        mapDiv.innerHTML = '<p>Invalid map format. Content: "' + directionContent.substring(0, 100) + '..."</p>';
                    }
                }
                
                document.getElementById('fuelpump-details').style.display = 'block';
                document.getElementById('fuelpump-details').scrollIntoView({ behavior: 'smooth' });
            }
        }

        function hideFuelPumpDetails() {
            document.getElementById('fuelpump-details').style.display = 'none';
        }
        </script>
    </main>
</body>
</html>
