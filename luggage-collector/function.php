<?php
/**
 * Created by IntelliJ IDEA.
 * User: PARMINDER
 * Date: 6/11/2015
 * Time: 5:55 PM
 */

function sms($url, $referer, $_data) {
    // convert variables array to string:
    $data = array();
    while (list($n, $v) = each($_data)) {
        $data[] = "$n=$v";
    }
    $data = implode('&', $data);
    // format --> test1=a&test2=b etc.
    // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') {
        die('Only HTTP request are supported !');
    }
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
    // open a socket connection on port 80
    $fp = fsockopen($host, 80);
    // send the request headers:
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);
    $result = '';
    while (!feof($fp)) {
        // receive the results of the request
        $result .= fgets($fp, 128);
    }
    // close the socket connection:
    fclose($fp);
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';

    // return as array:
    return array($header, $content);
}


function userDetails($uuid){

    //fetching user_id related with uuid
    $query="select user_id from uuid where uuid=".$uuid;
    $query=mysql_query($query);
    $result=mysql_fetch_assoc($query);
    //$result['user_id'];

    //fetching user details using user id from last query
    $query="select * from user where id=".$result['user_id'];
    $query=mysql_query($query);
    $result=mysql_fetch_assoc($query);
    $string=$result['name']."_%_".$result['phone']."_%_".$result['weight'];
    return $string;
}


?>