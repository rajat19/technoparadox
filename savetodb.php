<?php
include 'includes/functions.php';
$user = new User();
$user->addLoc($_SESSION['google_data']['id'], $add[0], $add[1]);
?>