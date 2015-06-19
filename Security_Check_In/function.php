<?php
require_once('../db.inc.php');
//setting time zone of india
date_default_timezone_set("Asia/Kolkata");

//current time
function currentTime(){
    $current = date("h:i:s");
    $current = explode(":", $current);
    //change time into min
    $time_min = ($current[0] * 60) + ($current[1]);
    return $time_min;

}

function timeGap($user_id)
{    // timeGap function will help to figure out that it is either emergy or norrmal case
    //getting current timme
     $time_min=currentTime();
   // selecting flight_id of user
    $query="select flight_id from user where id=".$user_id;
    $query=mysql_query($query);
    $flight_id = mysql_fetch_assoc($query);

//fetching time of that particular flight
    $query="select time from flight where id=".$flight_id['flight_id'];
    $query=mysql_query($query)or die(mysql_error());
    $flight_time = mysql_fetch_assoc($query);

//changing time into min
    $current=$flight_time['time'];
    $current = explode(":", $current);
    $flight_time=($current[0] * 60) + ($current[1]);

//cal time diff
    $time_diff = $flight_time-$time_min;
    //echo $time_diff;

//taking 45 min as a threshold time ... means on average it take 45 min from security check to boarding
    if($time_diff<46 && $time_diff>0){
       $avail=emergencyCase($user_id);
        getNoOfPerson($avail,"e");

    }
    else{
         $avail=noramlCase($user_id);
          getNoOfPerson($avail,"n");

    }

}


function emergencyCase($user_id){
    $counter=0;
    $query="select id from security_details where cat= 'emergencyCase'";
    $query=mysql_query($query)or die(mysql_error());
    while($result = mysql_fetch_assoc($query)){
        $send[$counter]=$result['id'];
        $counter++;
    }
    return($send);
}

function noramlCase($user_id){
    $counter=0;
    $query="select id from security_details where cat= 'noramlCase'";
    $query=mysql_query($query)or die(mysql_error());
    while($result = mysql_fetch_assoc($query)){
       $send[$counter]=$result['id'];
        $counter++;
    }

    return($send);
}

function getNoOfPerson($avail,$s){
    if($s=='e'){
        //for emergencyCase
        $counter=0;
        $numberList= array();
        //numberList i am storing the number of person standing in that
        //$avail is an array contains value of different value of availble counter
        while($avail[$counter]){
            $query="select number from security_details where id=".$avail[$counter];
            $query=mysql_query($query)or die(mysql_error());
            $result = mysql_fetch_assoc($query);
            $numberList[$counter]=$result['number'];
            $counter++;
        }
        return $avail[array_search(min($numberList),$numberList)];


    }
    elseif($s=='n'){
        //for noramlCase
        $counter=0;
        $numberList= array();
        //numberList i am storing the number of person standing in that
        //$avail is an array contains value of different value of availble counter
        while($avail[$counter]){
//            echo $avail[$counter]."</br>";
            $query="select number from security_details where id=".$avail[$counter];
            $query=mysql_query($query)or die(mysql_error());
            $result = mysql_fetch_assoc($query);
            $numberList[$counter]=$result['number'];
            $counter++;
        }

        $return = $avail[array_search(min($numberList),$numberList)];

        //check if emergy are available
        checkEmergencyLane();

     }

}

function checkEmergencyLane(){
    //provides current time in min add for 30 min
    $currentTime=currentTime()+30;
    $timeC=date('h:i');
    //convert the time(min form) into hour:min (after 30 min)type
    $hour=intval($currentTime/60);
    $min=$currentTime%60;
    if($min<10)
        $min="0".$min;
    //converting string to time
    $time= date("H:i", strtotime("$hour:$min"));
    $time_diff="45";
    //check for next 30min ,if flight is there and all customers have already boarder or not.
     // $query="SELECT id FROM flight WHERE {`$time`-`time`} < 2700 AND {`$time`-`time`} < 0 ";
   // $query="SELECT * from flight where {(TIMEDIFF(`time`,`$time`)) = `$time_diff`}";
//    $query="select id from flight where ((`time`)-(`$time`))>0";
    $query="SELECT id FROM flight WHERE time BETWEEN '".$timeC."' AND '".$time."'";
    echo $query;
    $query=mysql_query($query)or die(mysql_error());
    echo $query;
    $result = mysql_fetch_assoc($query);
    echo $result['id']."<br>";
   if($result = mysql_fetch_assoc($query)) {
       while ($result = mysql_fetch_assoc($query)) {
           echo "parmminder" . $result['id'];
       }
   }
    else{

        echo "parmidner";
    }
  //  SELECT TIMESTAMPDIFF(MINUTE, `time`, $time) FROM `flight`
}
?>