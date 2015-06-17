<?php
   require_once('../db.inc.php');
  //reomve this line after testing,because this can b included in index.php on this package

/**  cases
 1.  fetch the lane available for "this"
 2. from above lane select the best(less no  of persons , less no of person of same flight)
 3. check if it can b provided for the better
 **/

//setting time zone of india
date_default_timezone_set("Asia/Kolkata");

//varaible for functions
$user_id="1";

function timeGap($user_id)
{
    $current = date("h:i:s");
    $current = explode(":", $current);
    //changing the current time into minutes
    $time_min = ($current[0] * 60) + ($current[1]);

    //selecting flight_id of user
    $query="select flight_id from user where id=".$user_id;
    $query=mysql_query($query);
    $flight_id = mysql_fetch_assoc($query);

    //fetching time of that particular flight
    $query="select time from flight where id=".$flight_id['flight_id'];
    $query=mysql_query($query);
    $flight_time = mysql_fetch_assoc($query);

    //changing time into min
    $current=$flight_time['time'];
    $current = explode(":", $current);
    $flight_time=($current[0] * 60) + ($current[1]);

    //cal time diff
    $time_diff = $flight_time-$time_min;

     //taking 45 min as a threshold time ... means on average it take 45 min from security check to boarding
    if($time_diff>46){
        emergencyCase($user_id);
    }
    else{

        noramlCase($user_id);
    }

}


function emergencyCase($user_id){

    echo "emergency";
}

function normalCase($user_id){


    echo "noraml case";
}


?>
