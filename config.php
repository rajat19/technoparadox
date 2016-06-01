<?php
session_start();
include_once("src/Google_Client.php");
include_once("src/contrib/Google_Oauth2Service.php");
######### edit details ##########
$clientId = '1067683500763-qp895mbdn79avjabjadl2j7e4nvd5rfi.apps.googleusercontent.com'; //Google CLIENT ID
$clientSecret = '6pFruwawvTQr94ZFR4xeIo6_'; //Google CLIENT SECRET
$redirectUrl = 'http://localhost/technoparadox';  //return url (url to script)
$homeUrl = 'http://localhost/technoparadox';  //return to home

##################################

$gClient = new Google_Client();
$gClient->setApplicationName('Login to Paradox.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectUrl);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>