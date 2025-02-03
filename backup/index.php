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
</head>
<body>

<h1>Your City is <?=$json['city']?></h1>

<?php

echo '<pre>';
print_r($json);
echo '</pre>';

?>

</body>
</html>