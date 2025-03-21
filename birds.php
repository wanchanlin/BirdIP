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

// Check if response is valid
if (!$json || isset($json['error'])) {
    die('Error fetching geolocation data.');
}

$latitude = $json['latitude'] ?? null;
$longitude = $json['longitude'] ?? null;
$city = $json['city'] ?? 'Unknown';
$region = $json['region_name'] ?? 'Unknown';
$country = $json['country_name'] ?? 'Unknown';

// Get bird sightings
$bird_data = [];
if ($latitude && $longitude) {
    try {
        $bird_data = get_recent_bird_sightings($latitude, $longitude);
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
</head>
<body>
<section>
<center>
<h1><?= "{$city}, {$region}, {$country}" ?></h1></center>
<div class="container">

    <?php if (!empty($bird_data)): ?>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Species Name</th>
                    <th>Scientific Name</th>
                    <th>Location</th>
                    <th>More Info</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1; // Initialize counter
                foreach ($bird_data as $bird): ?>
                    <tr>
                        <td>
                            <span class="rank"><?= $count++ ?></span> <!-- Increment counter -->
                        </td>
                        <td><?= htmlspecialchars($bird['comName']) ?></td>
                        <td><?= htmlspecialchars($bird['sciName'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($bird['locName'] ?? 'Unknown') ?> 
                        <span style="color:#000" class="btn"><a href="https://en.wikipedia.org/w/index.php?search=<?= urlencode($bird['comName']) ?>" target="_blank">
                                View More
                            </a>
                        </span>
                        </td>
                   
                            
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No recent bird sightings found.</p>
    <?php endif; ?>
</div>
</section>
</body>
</html>
