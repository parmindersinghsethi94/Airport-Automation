<?php
//uncomment sms code before running
require_once ('../db.inc.php');
require('function.php');
// $uuid=$_REQUEST['uuid'];
$uuid="ffe4d519d016599e8ec0ff785b0be9bd";
$details=userDetails($uuid);
$userData=explode("_%_",$details);
//$userData[0] status
//$userData[1] name
//$userData[2] number
$number='91'.$userData[2];

if($userData[0]=='a'){
    $message = "Hello ".$userData[1]." ,pick your bag ";
	$a=updateStatus($userData[0],$uuid);
    echo json_encode($message);
	
	 
}
elseif($userData[0]=='b'){
    $message = "Hello ".$userData[1].", skip ";
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

// echo json_encode($data);
 

list($header, $content) = sms(
    "http://www.smslane.com//vendorsms/pushsms.aspx", // the url to post to
    "http://www.facebook.com", // its your url
     $data
 );
?>