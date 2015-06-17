<?php
/**
 * Created by IntelliJ IDEA.
 * User: PARMINDER
 * Date: 6/11/2015
 */

define("HOST", "localhost");
// Database user
define("DBUSER", "root");
// Database password
define("PASS", "");
// Database name
define("DB", "airport");
$conn = mysql_connect(HOST, DBUSER, PASS) or  die('Could not connect !<br />Please contact the site\'s administrator.');
$db = mysql_select_db(DB) or  die('Could not connect to database !<br />Please contact the site\'s administrator.');
$seed="0dAfghRqSTgx"; // the seed for the passwords
$domain =  "patakadeals.com"; // the domain name without http://www.

?>
