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

function getUsers($DB){

    $result = $DB->get_records_sql('SELECT p.points,u.firstname, u.lastname, u.id FROM {block_oa_points} AS p INNER JOIN {user} AS u ON p.user=u.id
                                    ORDER BY p.points DESC');
  // WHERE foo = ? AND bob = ?', array( 'bar' , 'tom' ));
 //   $users = $DB->get_records('block_oa_points', null);
    return $result;

}


function createNewUser($UserId, $DB){
   $fieldId = $DB->insert_record('block_oa_points', array('user'=>$UserId, 'points' => 0), true);
    return $fieldId;
}

function increaseUsersPoints($UserId,$DB,$diff){
    $userPoints = $DB->get_record('block_oa_points', array('user'=>$UserId));
    if(!$userPoints){
        $fieldId = createNewUser($UserId,$DB);
        $points=0;
    }
    else{
        $points=$userPoints->points;
        $fieldId = $userPoints->id;
    }

    $DB->update_record('block_oa_points', array('id'=>$fieldId, 'points' => $points+$diff));

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