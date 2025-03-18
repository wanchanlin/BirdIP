<?php

function get_user_ip() {
    $ip_sources = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    $ip = 'UNKNOWN';

    foreach ($ip_sources as $source) {
        if (isset($_SERVER[$source])) {
            $ip_list = explode(',', $_SERVER[$source]);
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    return $ip;
}
function get_recent_bird_sightings($lat, $lng) {
    if (!defined('EBIRD_API_KEY')) {
        throw new Exception('eBird API key not configured');
    }
    
    $url = "https://api.ebird.org/v2/data/obs/geo/recent?lat={$lat}&lng={$lng}";
    $headers = [
        'X-eBirdApiToken: ' . EBIRD_API_KEY,
        'Accept: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);


    if (curl_errno($ch)) {
        throw new Exception("API request failed: " . curl_error($ch));
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception("eBird API returned HTTP $http_code");
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON decode error: " . json_last_error_msg());
    }

    return $result ?? [];
}
 
