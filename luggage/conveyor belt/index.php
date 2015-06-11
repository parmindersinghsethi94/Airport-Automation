<?php
//uncomment sms code before running
require_once ('../db.inc.php');
require('function.php');

//$uuid=$_REQUEST['uuid'];
$uuid="6965";
$details=userDetails($uuid);
$userData=explode("_%_",$details);
//$userData[0] status
//$userData[1] name
//$userData[2] number
//$userData[3] weight
$number=$userData[2];
if($userData[0]=='a'){
    $message = "Hello ".$userData[1]." ,pick your bag and weigth of your bag is : ".$userData[3] ;
    updateStatus($userData[0],$uuid);
}
elseif($userData[0]=='b'){
    $message = "Hello ".$userData[1].", skip and weigth of your bag is : ".$userData[3] ;
    updateStatus($userData[0],$uuid);
}
$data = array(
    'user' => "patakadeals",
    'password' => "patakadeals",
    'msisdn' => $number,
    'sid' => "WEBSMS",
    'msg' => $message,
    'fl' => "0",
);

//list($header, $content) = sms(
  //  "http://www.smslane.com//vendorsms/pushsms.aspx", // the url to post to
    //"http://www.facebook.com", // its your url
    //$data
//);
?>