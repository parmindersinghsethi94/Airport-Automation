<?php
require_once ('db.inc.php');
require('function.php');

//$uuid=$_REQUEST['uuid'];
$uuid="6965";
$details=userDetails($uuid);
$userData=explode("_%_",$details);
//$userData[0] name
//$userData[1] number
//$userData[2] weight
$number=$userData[1];
$message = "Hello ".$userData[0]." ,pick your bag and weigth of your bag is : ".$userData[2] ;
$data = array(
    'user' => "patakadeals",
    'password' => "patakadeals",
    'msisdn' => $number,
    'sid' => "WEBSMS",
    'msg' => $message,
    'fl' => "0",
);

list($header, $content) = sms(
    "http://www.smslane.com//vendorsms/pushsms.aspx", // the url to post to
    "http://www.facebook.com", // its your url
    $data
);
?>