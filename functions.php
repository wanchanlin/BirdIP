<?php


/*
 * This function was copied from:
 * https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
 */
function get_user_ip()
{
    // List of server variables that may contain the client's IP address
    $ip_sources = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    $ip = false;

    // Iterate over each source to find a valid IP address
    foreach ($ip_sources as $source) 
    {
        if (getenv($source)) 
        {
            $ip = getenv($source);
            // Break the loop if a valid IP is found
            if (filter_var($ip, FILTER_VALIDATE_IP)) 
            {
                break;
            }
        }
    }

    // Final validation: ensure the IP is not the server's IP
    if ($ip && ($ip == getenv('SERVER_ADDR') || !filter_var($ip, FILTER_VALIDATE_IP))) 
    {
        $ip = false;
    }

    return $ip;
}