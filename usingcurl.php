<?php
// Get cURL resource
$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSLVERSION,3);
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=28.6786045,77.4989313&destinations=17.1579055786%2C82.0466461182&key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE',
    CURLOPT_USERAGENT => 'Rajat Srivastava'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
if(!curl_exec($curl)){
    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
}
// Close request to clear up some resources
curl_close($curl);

/*
https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=40.6655101,-73.89188969999998&destinations=40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.659569%2C-73.933783%7C40.729029%2C-73.851524%7C40.6860072%2C-73.6334271%7C40.598566%2C-73.7527626%7C40.659569%2C-73.933783%7C40.729029%2C-73.851524%7C40.6860072%2C-73.6334271%7C40.598566%2C-73.7527626&key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE
*/

// if(isset($_POST['setlocg'])) {
//     // Set some options - we are passing in a useragent too here
//     curl_setopt_array($curl, array(
//         CURLOPT_RETURNTRANSFER => 1,
//         CURLOPT_URL => 'https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE',
//         CURLOPT_USERAGENT => 'Rajat Srivastava',
//         CURLOPT_POST => 1,
//         CURLOPT_POSTFIELDS => array(
        
//         )
//     ));
//     // Send the request & save response to $resp
//     $resp = curl_exec($curl);
//     if(!curl_exec($curl)){
//         die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
//     }
//     // Close request to clear up some resources
//     curl_close($curl);

//     $locg = json_decode($resp, true);
//     // print_r($locg);
//     $lat = $locg['location']['lat'];
//     $lon = $locg['location']['lng'];
//     $_SESSION['lat'] = $lat;
//     $_SESSION['lon'] = $lon;
//     $user->addLoc($_SESSION['google_data']['id'], $lat, $lon);
// }
$json = json_decode($resp, true);
print_r($json);
?>
