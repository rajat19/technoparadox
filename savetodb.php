<?php
session_start();
if(!isset($_SESSION['google_data'])):header("Location:index.php");endif;

include 'includes/functions.php';
$user = new Users();
$_SESSION['lat'] = $lat = $_GET['lat'];
$_SESSION['lon'] = $lon = $_GET['lon'];
$user->addLoc($_SESSION['google_data']['id'], $lat, $lon);

echo "Location changed to $lat, $lon<br>";
?>