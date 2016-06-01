<?php
session_start();
if(!isset($_SESSION['google_data'])):header("Location:index.php");endif;
?>

<script type="text/javascript">
    function getgooglelocation() {
        var add = "https://www.googleapis.com/geolocation/v1/geolocate?key=";
        var key = "AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE";
        var jsonLatLong;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                jsonLatLong = xmlhttp.responseText;
                document.getElementById("txtHint").innerHTML = jsonLatLong;
                console.log(jsonLatLong);
                var obj = JSON.parse(jsonLatLong);
                var lat = obj.location.lat;
                var lon = obj.location.lng;
                savetodb(lat,lon);
            }
        };
        xmlhttp.open("POST", add + key, true);
        xmlhttp.send();  
    }

    function getmanuallocation() {
        var add = "getaddress.php";

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("hiddiv").innerHTML = xmlhttp.responseText;
            }
        };
        xmlhttp.open("POST", add, true);
        xmlhttp.send();   
    }

    function savetodb(lat,lon) {
        var add = "savetodb.php?lat="+lat+"&lon="+lon;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        };
        xmlhttp.open("GET", add, true);
        xmlhttp.send();
    }
</script>
<p id="txtHint"></p>
<?php
include 'includes/functions.php';
$profile = array(); $nearest = array(); $myloc = array();
$profile['oauth_uid'] = $_SESSION['google_data']['id'];
$profile['picture'] = $_SESSION['google_data']['picture'];
$profile['name'] = $_SESSION['google_data']['name'];
$profile['gender'] = $_SESSION['google_data']['gender'];
$profile['email'] = $_SESSION['google_data']['email'];

$user = new Users();
$myloca = $user->getLoc($_SESSION['google_data']['id']);
$profile['lat'] = $_SESSION['lat'] = $myloca['lat'];
$profile['lon'] = $_SESSION['lon'] = $myloca['lon'];

print_r($profile);
echo '<p><b>Location detection : </b><button onclick="getgooglelocation();">Get Location</button><form action="apiformat.php" method="POST"><input type="submit" value="Set Location" name="locdetect"></form></p>';
echo '<p><b>Find nearest friends : </b><form action="apiformat.php" method="POST"><input type="submit" value="Find Active people" name="near"></form></p>';

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSLVERSION,3);

if(isset($_POST['locdetect'])) {
    echo '<form action="apiformat.php" method="POST">
    <select name="list">';
        // $conn = mysqli_connect('localhost', 'root', '', 'technoparadox');
    	$conn = $user->connect;
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
	$id = $_SESSION['google_data']['id'];
    $q = $user->findPeople($id);
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
	echo "JSON => <br>";
	print_r($json);
	echo "<br><br>";
	echo "Array => <br>";
	echo "<pre>";
	print_r($final);
	echo "</pre>";
	
}
?>