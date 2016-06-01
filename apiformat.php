<?php
session_start();
if(!isset($_SESSION['google_data'])):header("Location:index.php");endif;

include 'includes/functions.php';
$profile = array(); $nearest = array(); $myloc = array();
$profile['oauth_uid'] = $_SESSION['google_data']['id'];
$profile['picture'] = $_SESSION['google_data']['picture'];
$profile['name'] = $_SESSION['google_data']['name'];
$profile['gender'] = $_SESSION['google_data']['gender'];
$profile['email'] = $_SESSION['google_data']['email'];
$profile['lat'] = $_SESSION['lat'];
$profile['lon'] = $_SESSION['lon'];

print_r($profile);
echo '<p><b>Location detection : </b><form action="apiformat.php" method="POST"><input type="submit" value="Get Location" name="setlocg"></form><form action="apiformat.php" method="POST"><input type="submit" value="Set Location" name="locdetect"></form></p>';
echo '<p><b>Find nearest friends : </b><form action="apiformat.php" method="POST"><input type="submit" value="Find Active people" name="near"></form></p>';

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSLVERSION,3);
$user = new Users();
if(isset($_POST['locdetect'])) {
    echo '<form action="apiformat.php" method="POST">
    <select name="list">';
        $conn = mysqli_connect('localhost', 'root', '', 'technoparadox');
        $q1 = mysqli_query($conn, "SELECT * FROM locations ORDER BY city");
        while($row = mysqli_fetch_array($q1)) {
            $state = $row['state'];
            $city = $row['city'];
            $id = $row['id'];
            $lat = $row['lat'];
            $lon = $row['lon'];
            $arr = implode('$', array($lat, $lon));
            echo "<option value='$arr'>$city, $state</option>";
        }
    echo '</select>
        <input type="submit" name="setlocm" value="Enter the location">
    </form>';
}

if(isset($_POST['setlocg'])) {
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE',
        CURLOPT_USERAGENT => 'Rajat Srivastava',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
        
        )
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    if(!curl_exec($curl)){
        die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    }
    // Close request to clear up some resources
    curl_close($curl);

    $locg = json_decode($resp, true);
    // print_r($locg);
    $lat = $locg['location']['lat'];
    $lon = $locg['location']['lng'];
    $_SESSION['lat'] = $lat;
    $_SESSION['lon'] = $lon;
    $user->addLoc($_SESSION['google_data']['id'], $lat, $lon);
}
if(isset($_POST['setlocm'])) {
    $add = explode("$", $_POST['list']);
    // print_r($add);
    $lat = $add[0];
    $lon = $add[1];
    $_SESSION['lat'] = $lat;
    $_SESSION['lon'] = $lon;
    $user->addLoc($_SESSION['google_data']['id'], $lat, $lon);
}

if(isset($_POST['near'])) {
    $q = $user->findPeople($_SESSION['google_data']['id']);
    $userlat = $_SESSION['lat'];
    $userlon = $_SESSION['lon'];
    $address = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$userlat,$userlon&destinations=";
    $id = 0;$a = array();$b= array();
    while($row = mysqli_fetch_array($q)) {
        $flat = $row['lat'];
        $flon = $row['lon'];
        $fid = $row['oauth_uid'];
        $newadd = $address."$flat%2C$flon&key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE";
        $dist = $user->calculatedistance($newadd);
        $val = $dist['rows'][0]['elements'][0]['distance']['value'];
        $miles = $dist['rows'][0]['elements'][0]['distance']['text'];
        $a[$fid] = $val;
        $b[$fid] = $miles;
    }
    asort($a);
    $id = 0;
    foreach($a as $uid=>$val) {
        $q = $user->userdetails($uid);
        $localname = $q['fname']." ".$q['lname'];
        // echo $q['fname']." ".$q['lname']." => ".$val."<br>";
        $near['people'][$id]['name'] = $localname;
        $near['people'][$id]['distance'] = $b[$uid];
        $id++;
    }
    finalapi($profile, $near, $myloc);
}

function finalapi($profile, $near, $myloc) {
	$myloc['lat'] = $_SESSION['lat'];
	$myloc['lon'] = $_SESSION['lon'];
	$final = array();
	$final[0]['mydetails'] = $profile;
	$final[0]['mylocation'] = $myloc;
	$final[0]['nearest'] = $near;

	$json = json_encode($final);
	echo "Array => <br>";
	echo "<pre>";
	print_r($final);
	echo "</pre>";
	echo "JSON => <br>";
	print_r($json);
}
?>