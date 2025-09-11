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
<body class="workshop">
    <?php
    session_start();
    $profilePicPath = "img/default_profile.png";
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
    }
    ?>
    <header>
        <nav>
            <div class="nav_logo">
                <h1><a href="home.php">2nd Hand Car Advisor</a></h1>
            </div>
            <ul class="nav_link">
                <li><a href="home.php">Home</a></li>
                <li><a href="car_model.php">Car Models</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
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
        <section class="Workshops">
            <div class="carmodel_box">
                <h1>Workshops</h1>
                <table class="carmodel_table">
                    <thead>
                        <tr>
                            <th>Workshop Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        require_once("DBconnect.php");
                        $sql = "SELECT * FROM workshop";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            $workshop_data = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Debug: Check what's actually in the direction field
                                $direction = isset($row['direction']) ? $row['direction'] : '';
                                
                                $workshop_data[] = [
                                    'name' => $row['Workshop_Name'],
                                    'location' => $row['Workshop_Location'],
                                    'contact' => $row['Workshop_Contact'],
                                    'direction' => $direction
                                ];
                                
                                echo '<tr>';
                                echo '<td>';
                                echo '<a href="#" class="workshop-link" onclick="showWorkshopDetails(' . (count($workshop_data) - 1) . '); return false;">' . 
                                     htmlspecialchars($row['Workshop_Name']) . '</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td>No workshops found.</td></tr>';
                            $workshop_data = [];
                        }
                        ?>
                        <script>
                        const workshopData = <?php echo json_encode($workshop_data); ?>;
                        console.log('All workshop data with directions:', workshopData);
                        </script>
                    </tbody>
                </table>
            </div>

            <!-- Workshop Details Section -->
            <div id="workshop-details" class="carmodel_box" style="display: none; margin-top: 20px;">
                <h2>Workshop Details</h2>
                <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 10px; color: #333;">
                    <p><strong>Name:</strong> <span id="detail-name"></span></p>
                    <p><strong>Location:</strong> <span id="detail-location"></span></p>
                    <p><strong>Contact:</strong> <span id="detail-contact"></span></p>
                    <div id="detail-map" style="margin-top: 15px;"></div>
                    <button onclick="hideWorkshopDetails()" style="margin-top: 15px; padding: 10px 20px; background: #2a5d9f; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                </div>
            </div>
        </section>

        <script>
        function showWorkshopDetails(index) {
            if (workshopData && workshopData[index]) {
                const workshop = workshopData[index];
                console.log('Selected workshop:', workshop);
                console.log('Direction field value:', workshop.direction);
                
                document.getElementById('detail-name').textContent = workshop.name;
                document.getElementById('detail-location').textContent = workshop.location;
                document.getElementById('detail-contact').textContent = workshop.contact;
                
                const mapDiv = document.getElementById('detail-map');
                
                if (workshop.direction === null || workshop.direction === undefined || workshop.direction.trim() === '') {
                    mapDiv.innerHTML = '<p>No map available for this workshop.</p>';
                } else {
                    // Check if it's a complete iframe HTML or just a URL
                    let directionContent = workshop.direction.trim();
                    
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
                
                document.getElementById('workshop-details').style.display = 'block';
                document.getElementById('workshop-details').scrollIntoView({ behavior: 'smooth' });
            }
        }

        function hideWorkshopDetails() {
            document.getElementById('workshop-details').style.display = 'none';
        }
        </script>
    </main>
</body>
</html>
