<?php
include_once("config.php");
include_once('includes/functions.php');
$users = new Users();
if(array_key_exists('logout',$_GET))
{
	$users->setLogout($_SESSION['google_data']['id']);
	unset($_SESSION['token']);
	unset($_SESSION['google_data']); //Google session data unset
	$gClient->revokeToken();
	session_destroy();
	header("Location:index.php");
}
?>