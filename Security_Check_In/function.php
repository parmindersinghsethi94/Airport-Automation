<?php
require_once('../db.inc.php');
//setting time zone of india

date_default_timezone_set("Asia/Kolkata");

//current time
function currentTime(){
    $current = date("H:i:s");
    $current = explode(":", $current);
    //change time into min
    $time_min = ($current[0] * 60) + ($current[1]);
    return $time_min;

}

function timeGap($user_id)
{   // timeGap function will help to figure out that it is either emergy or norrmal case
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
    $current = str_split($current);
    $current[0]=$current[0].$current[1];//hour
    $current[2]=$current[2].$current[3];//min
    $flight_time=($current[0] * 60) + ($current[2]); //total time in minutes
    $time_diff = $flight_time-$time_min;

//taking 45 min as a threshold time ... means on average it take 45 min from security check to boarding
    if($time_diff<46 && $time_diff>0){
       $avail=emergencyCase();
        //$avail is list of all the lanes in emergencyCase
       return getBestLane($avail,"e");

    }
    else{
         $avail=noramlCase();
        //$avail is list of all the lanes in noraml case
       return getBestLane($avail,"n");

    }

}


function emergencyCase(){
    $counter=0;
    $query="select id from security_details where cat= 'emergencyCase'";
    $query=mysql_query($query)or die(mysql_error());
    while($result = mysql_fetch_assoc($query)){
        $send[$counter]=$result['id'];
        $counter++;
    }
    return($send);
}

function noramlCase(){
    $counter=0;
    $query="select id from security_details where cat= 'noramlCase'";
    $query=mysql_query($query)or die(mysql_error());
    while($result = mysql_fetch_assoc($query)){
       $send[$counter]=$result['id'];
        $counter++;
    }

    return($send);
}

function getBestLane($avail,$s){
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
        //$counter=0;
       // $numberList= array();
        //numberList i am storing the number of person standing in that
        //$avail is an array contains value of different value of availble counter
//        while($avail[$counter]){
////            echo $avail[$counter]."</br>";
//            $query="select number from security_details where id=".$avail[$counter];
//            $query=mysql_query($query)or die(mysql_error());
//            $result = mysql_fetch_assoc($query);
//            $numberList[$counter]=$result['number'];
//            $counter++;
//        }

        //returns the value in case emergency lane is not availble for next 30 min
        //$return_default = $avail[array_search(min($numberList),$numberList)];


                //check if emergy are available
                $return=checkEmergencyLane();
              // if sorry is not coming from the checkEmergencyLane , means some lane is availble for next noraml case customers
//                if($return != "sorry")
                    return $return;
//                else
//                    return $return_default;

     }

}

function checkEmergencyLane(){
    $avgTimeForOnePerson=10;
    //average time(in normal condition) for each person after collecting boarding pass to pass the security barrier

    //variable $no which stores the number  of customers who havent got boarding pass
    $no=0;
    //provides current time(in min)add for 30 min
    $currentTime=currentTime()+30;
    //convert the time(min form) into hour:min (after 30 min)type
    $hour=intval($currentTime/60);
    $min=$currentTime%60;
    //adding zero if hour or min is in single digit
    if ($hour<10)
        $hour="0".$hour;
    if($min<10)
        $min="0".$min;

    //converting time to string
    $time=$hour.$min;

    //getting the current time in form of string
    $current = date("H:i");
    $current = explode(":", $current);
    $current= $current[0].$current[1];
    //fetching id of flights in next 30min
    $query="select id from flight where time < '".$time."' and time > '".$current."'";
    $query=mysql_query($query)or die(mysql_error());
    //$result = mysql_fetch_assoc($query);

    if($query) {
        //this means there is some flight in next 30 min
        while ($result = mysql_fetch_assoc($query)) {
          //now check the status of each customer of  that particular flight
           $no+= getCustomerStatus($result['id']);
        }

        //in the end of while loop $no will be containing total no of person that are going to coming and having flight in next 30 min
        if($no!=0) {
            //$no --> no of pending persons for next 30 min
            //$noOfEmergencyLane contains the total no of emgerncy lane
            //$getStanding contains total person standing in emergencyLane
            $emergencyLaneId = emergencyCase(); //list of id for emergency Lane
            $temp = getNoOfPersonStandingEmergency($emergencyLaneId);
            $temp = explode(":", $temp);
            $totalPersonStanding = $temp[0];
            $noOfEmergencyLane = $temp[1];
            //(total pending person + no standing person)/(no of emeg lane)
            $avgCapacityOfOneEmergencyLane=intval(($no+$totalPersonStanding)/$noOfEmergencyLane);
            echo $avgCapacityOfOneEmergencyLane;
            $averageTimeForEachLane=$avgTimeForOnePerson*$avgCapacityOfOneEmergencyLane;
            //this will tell me the average time for making that queue empty
            if($averageTimeForEachLane<30){
                //means emergencyCase  lane can be allocated.so find lane with minimum no of persons
               return getMinEmergencyLane();
            }
            else{
                return getMinLane();
            }
        }
        else{
            //in this case there is flight and every1 has already bored so all the lanes are option for this moment
            return getMinLane();
        }
    }

    else{
        //means no flight in next 30 min so emergency lanes will be avialble
        //so we can check that in which lane it less no of people are present and then compare with the noraml lane rush. what so ever is minimum that will be allocated
        // so finding the min number value in security lane and assign to the customer
        $result = getMinLane();
        return $result; //this lane will be alloacted to customer
    }


}

//this function return the no of customers that are boared and those are not boarded


function getCustomerStatus($flight_id){
    //this function will return the no customer that havent got the boarding pass
    //finding all the id of customers visiting in this flight  and then we can check either they are having UUID,yes means they got boarding pass and no means they are no
    return getNotBoardUserId($flight_id);

}

function getNotBoardUserId($flight_id){
    $yes=0; //all who has boarding pass
    $no=0; ///all who is not having
    //this will fetch all the users who will be going in that flight and not check in
    $query="select id from user where flight_id=".$flight_id;
    $query=mysql_query($query)or die(mysql_error());
    //this will fetch out the id of user for more optimization
    while($result = mysql_fetch_assoc($query)){
       $counter = getUuidStatus($result['id']);
        if($counter==1)
            $yes+=1;
        elseif($counter==0)
            $no+=1;
    }
    //return the number of customers without the boarding point
    return $no;
}
function getUuidStatus($user_id){
    //return that either user_id has uuid or not .Acc to that i will return 1 or 0
    $query="select uuid from uuid where user_id =".$user_id;
    $query=mysql_query($query)or die(mysql_error());
    $result = mysql_fetch_assoc($query);
    if($result)
        return 1;
    else
        return 0;

}
function getMinEmergencyLane(){
    //this will return the emergency lane with minimum number of person standing
    $query="select id from security_details where( number <=(SELECT min(number) FROM security_details) and cat ='emergencyCase')";
    $query=mysql_query($query)or die(mysql_error());
    $result = mysql_fetch_assoc($query);
    return $result['id'];
}

function getMinLane(){
    //this will return the lane with minimum number of person standing(this include all the lanes)
    $query="select id from security_details where( number <=(SELECT min(number) FROM security_details))";
    $query=mysql_query($query)or die(mysql_error());
    $result = mysql_fetch_assoc($query);
    return $result['id'];
}
function getNoOfPersonStandingEmergency($noOfEmergencyLane)
{
    $totalPersonStanding=0;
    $counter = 0;
    while ($noOfEmergencyLane[$counter]){
        $query = "select number from security_details where id=".$noOfEmergencyLane[$counter];
        $query = mysql_query($query) or die(mysql_error());
        $result = mysql_fetch_assoc($query);
        $totalPersonStanding=$totalPersonStanding+$result['number'];
        $counter=$counter+1;
    }
    $return=$totalPersonStanding.":".($counter);
    //counter will be contains the valuw ( total no of lane +1) +1 for the last time when while fails
    return($return);

}

?>