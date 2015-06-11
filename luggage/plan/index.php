<?php
/**
 * Created by IntelliJ IDEA.
 * User: PARMINDER
 * Date: 6/11/2015
 * Time: 10:22 PM
 */

//uncomment sms code before running
require_once ('../db.inc.php');
require('function.php');

//$uuid=$_REQUEST['uuid'];
$uuid="12345";

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