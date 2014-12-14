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

function getQuestion($id){
    global $CFG;
    require('../../config.php');
    require('../../lib/questionlib.php');
    require('../../question/previewlib.php');

    $question = question_bank::load_question($id);

return var_dump($question);
}

function getQuestionsFromCategory($catId, $DB){
    $table = 'question';
    $conditions = array('category'=>$catId);
    $sort = null;
    $fields = 'id';
    $result = $DB->get_records($table,$conditions,$sort,$fields);
    return $result;
}