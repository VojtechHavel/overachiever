<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */

function getUsersPoints($UserId,$DB){

    $userPoints = $DB->get_record('block_oa_points', array('user'=>$UserId));
    if(!$userPoints){
        $points = 0;
    }
    else{
        $points=$userPoints->points;
    }
    return $points;

}