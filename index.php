<?php

// Load environment variables
$env = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($env as $value) {
    $value = explode('=', $value);
    define($value[0], $value[1]);
}

include('functions.php');

// Get user IP
$ip = get_user_ip();

// Get geolocation data
$url = 'https://api.ipstack.com/' . $ip . '?access_key=' . IPSTACK_ACCESS_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

curl_close($ch);
$json = json_decode($response, true);

// Get bird sightings
$bird_data = [];
if (!empty($json['latitude']) && !empty($json['longitude'])) {
    
    try {
        $bird_data = get_recent_bird_sightings($json['latitude'], $json['longitude']);
    } catch (Exception $e) {
        error_log('Error fetching bird data: ' . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bird IP</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initMap" async defer></script>
    <script>
        let map, infoWindow;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { 
                    lat: <?= $json['latitude'] ?? 0 ?>, 
                    lng: <?= $json['longitude'] ?? 0 ?> 
                },
                zoom: 6,
            });

            infoWindow = new google.maps.InfoWindow();

            const locationButton = document.createElement("button");
            locationButton.textContent = "Pan to Current Location";
            locationButton.classList.add("custom-map-control-button");
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);

            locationButton.addEventListener("click", () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };

                            infoWindow.setPosition(pos);
                            infoWindow.setContent("Location found.");
                            infoWindow.open(map);
                            map.setCenter(pos);
                        },
                        () => {
                            handleLocationError(true, infoWindow, map.getCenter());
                        }
                    );
                } else {
                    handleLocationError(false, infoWindow, map.getCenter());
                }
            });
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation
                    ? "Error: The Geolocation service failed."
                    : "Error: Your browser doesn't support geolocation."
            );
            infoWindow.open(map);
        }

        window.initMap = initMap;
    </script>
</head>
<body>
    <section>
     <center><h1>Bird IP</h1></center>
    <p>I have a deep appreciation for birds, drawn to their beauty, unique behaviors, and the sense of wonder they bring. Whether it's observing their vibrant colors, listening to their songs, or learning about different species, birds have always been a source of fascination. This passion extends to exploring bird habitats, understanding their role in ecosystems, and perhaps even capturing their elegance through photography or illustration.</p>
    <div> 
        <center>
            <img src="images/chick.svg" alt="">
            <img src="images/chick.svg" alt="">
            <img src="images/chick.svg" alt="">
        </center>
    </div>
    <div class="container">
        <div style="padding: 2rem; background-color: #1d1d1d; border-radius: 1rem; margin:.5rem auto;" class="IPCard">
            <div class="grid-container">
                <div class="grid-item">
                    <main>
                        <div>
                            <h3>Your IP address : </h3>
                            <div class="ip-box">
                                <p><?= $json['ip'] ?? 'Unknown' ?></p>
                            </div>
                        </div>
                        <div>
                            <h3>Your IP information: </h3>
                            <div class="ip-box">
                            <p><?= $json['city'] . ', ' . $json['region_name'] . ', ' . $json['country_name'] ?? 'Unknown' ?></p>
                            </div>
                        </div>
                        <div>
                            <h3>Local Birds:</h3>
                            <div class="ip-box">
                            <?php if (!empty($bird_data)): ?>
    <?php $count = 0; ?>
    <div class="top-birds-container">
        <?php foreach ($bird_data as $bird): ?>
            <?php if ($count < 5): ?>
                <div target="_blank" class="bird-list">
                    <span class="rank"><?= ++$count ?></span>
                    <a target="_blank" href="https://en.wikipedia.org/w/index.php?search=<?= urlencode($bird['comName']) ?>">
                    <span class="bird-name"><?= htmlspecialchars($bird['comName']) ?></a></span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
            <p>No recent bird sightings found.</p>
        <?php endif; ?>
    </div>
                        </div>
                    </main>
                    <a href="birds.php">
                        <button class="btn"> More Local Birds</button>
                    </a>
                    
                    </a>
                </div>
                <div class="grid-item">
                    <div id="map" style="height: 230px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    
    </section> 
    <!-- <?php
        print_r($bird_data);
    ?> -->


</body>
</html>
