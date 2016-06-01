<?php
session_start();
if(!isset($_SESSION['google_data'])):header("Location:index.php");endif;

include 'includes/functions.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login with Google Account by CodexWorld</title>
<style type="text/css">
h1
{
font-family:Arial, Helvetica, sans-serif;
color:#999999;
}
.wrapper{width:600px; margin-left:auto;margin-right:auto;}
.welcome_txt{
	margin: 20px;
	background-color: #EBEBEB;
	padding: 10px;
	border: #D6D6D6 solid 1px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
}
.google_box{
	margin: 20px;
	background-color: #FFF0DD;
	padding: 10px;
	border: #F7CFCF solid 1px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
}
.google_box .image{ text-align:center;}
</style>
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
                savetodb();
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
</script>
</head>
<body>
<div class="wrapper">
    <h1>Google Profile Details </h1>
    <?php
    $user = new Users();
    $myloc = $user->getLoc($_SESSION['google_data']['id']);
    $ulat = $_SESSION['lat'] = $myloc['lat'];
    $ulon = $_SESSION['lon'] = $myloc['lon'];
    echo '<div class="welcome_txt">Welcome <b>'.$_SESSION['google_data']['given_name'].'</b></div>';
    echo '<div class="google_box">';
    echo '<p class="image"><img src="'.$_SESSION['google_data']['picture'].'" alt="" width="300" height="220"/></p>';
    echo '<p><b>Google ID : </b>' . $_SESSION['google_data']['id'].'</p>';
    echo '<p><b>Name : </b>' . $_SESSION['google_data']['name'].'</p>';
    echo '<p><b>Email : </b>' . $_SESSION['google_data']['email'].'</p>';
    echo '<p><b>Gender : </b>' . $_SESSION['google_data']['gender'].'</p>';
    echo '<p><b>Locale : </b>' . $_SESSION['google_data']['locale'].'</p>';
    echo '<p><b>Google+ Link : </b>' . $_SESSION['google_data']['link'].'</p>';
    echo '<p><b>You are login with : </b>Google</p>';
    echo '<p><b>You current location : </b>'.$ulat.' , '.$ulon.'</p>';
    echo '<p><b>Location detection : </b><form action="account.php" method="POST"><input type="submit" value="Get Location" name="setlocg"></form><form action="account.php" method="POST"><input type="submit" value="Set Location" name="locdetect"></form></p>';
    echo '<p><b>Find nearest friends : </b><form action="account.php" method="POST"><input type="submit" value="Find Active people" name="near"></form></p>';
    echo '<p><b>Logout from <a href="logout.php?logout">Google</a></b></p>';
    echo '</div>';
    ?>
</div>
<p id="txtHint"></p>
<div id="hiddiv"></div>
<?php
// Get cURL resource
$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSLVERSION,3);

if(isset($_POST['locdetect'])) {
    echo '<form action="account.php" method="POST">
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
    print_r($locg);
    $lat = $locg['location']['lat'];
    $lon = $locg['location']['lng'];
    $_SESSION['lat'] = $lat;
    $_SESSION['lon'] = $lon;
    $user->addLoc($_SESSION['google_data']['id'], $lat, $lon);
}
if(isset($_POST['setlocm'])) {
    $add = explode("$", $_POST['list']);
    print_r($add);
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
    $id = 0;$a = array();
    while($row = mysqli_fetch_array($q)) {
        $flat = $row['lat'];
        $flon = $row['lon'];
        $fid = $row['oauth_uid'];
        $newadd = $address."$flat%2C$flon&key=AIzaSyAErQ52cVQDdBBVFFuHP5vBksvVEjWNXvE";
        $dist = $user->calculatedistance($newadd);
        $val = $dist['rows'][0]['elements'][0]['distance']['value'];
        $a[$fid] = $val;
    }
    asort($a);
    foreach($a as $uid=>$val) {
        $q = $user->userdetails($uid);
        echo $q['fname']." ".$q['lname']." => ".$val."<br>";
    }
}
?>
</body>
</html>