<?php

$env = file(__DIR__.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($env as $value)
{
  $value = explode('=', $value);  
  define($value[0], $value[1]);
}

include('functions.php');

$ip = get_user_ip();

$url = 'https://api.ipstack.com/'.$ip.'?access_key='.IPSTACK_ACCESS_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your City</title>
    <link rel="stylesheet" href="styles/style.css">
    <?php
echo '<script src="https://maps.googleapis.com/maps/api/js?key='.GOOGLE_MAPS_API_KEY.'&callback=initMap"
async defer></script>';
?>

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




<!-- Add your content here -->
<h1>Bird IP</h1>
    <p>I have a deep appreciation for birds, drawn to their beauty, unique behaviors, and the sense of wonder they bring. Whether it’s observing their vibrant colors, listening to their songs, or learning about different species, birds have always been a source of fascination. This passion extends to exploring bird habitats, understanding their role in ecosystems, and perhaps even capturing their elegance through photography or illustration</p>
    <div> <center><img src="images/chick.svg" alt=""> <img src="images/chick.svg" alt=""><img src="images/chick.svg" alt=""></center></div>
    <div class="container">
        <div style="padding: 2rem; background-color: #1d1d1d; border-radius: 1rem; margin:.5rem auto;" class="IPCard">
        
            <div class="grid-container">
                <div class="grid-item">
                    <main>
                        <div>
                            <h3>Your IP address : </h3>
                            <div class="ip-box">

                                <p><?=$json['ip']?></p>
                            </div>
                        </div>
                        <div>
                            <h3>Your IP information: </h3>
                            <div class="ip-box">
                                <p> <?=$json['city']?></p>
                            </div>
                        </div>
                        <div>
                            <h3>Local Birds:</h3>
                            <div class="ip-box">
                                <p>1. Northern Cardinal 
                                    2. American Robin 
                                    3. Indigo Bunting 
                                    4. Common Nighthawk 
                                    5. Long-tailed Duck 
                                    6. Black-Capped Chickadee 
                                    7. Turkey Vulture 
                                    8. Ruby-throated Hummingbird
                                 </p>
                            </div>
                        </div>
                    </main>

                </div>
                <div class="grid-item">
                    <div>

                        <div id="map" style=" height: 230px; width: 100%;"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php

// echo '<pre>';
// print_r($json);
echo '</pre>';

?>
</body>
</html>