<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../config.php');

function getUsersPoints($UserId,$DB){

    $userPoints = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$userPoints){
        $points = 0;
    }
    else{
        $points=$userPoints->points;
    }
    return $points;

}

function getUsers($DB){

    $result = $DB->get_records_sql('SELECT p.points,u.firstname, u.lastname, u.id FROM {block_oa_users} AS p INNER JOIN {user} AS u ON p.user=u.id
                                    ORDER BY p.points DESC');
  // WHERE foo = ? AND bob = ?', array( 'bar' , 'tom' ));
 //   $users = $DB->get_records('block_oa_users', null);
    return $result;

}


function createNewUser($UserId, $DB){
   $fieldId = $DB->insert_record('block_oa_users', array('user'=>$UserId, 'points' => 0), true);
    //returns id of the new field
    return $fieldId;
}

function questionAnswered($params){

    if($params['fraction']==1){
       return array('pointsinc'=>increaseUsersPoints());
    }
    return false;
}

function getCurrentQuestionId(){
    global $DB, $USER;
    $row = $DB->get_record('block_oa_current', array('user'=>$USER->id));
    return $row;
}

function questionSurvived($questionId, $fraction){
    insertSurvivedQuestion($questionId);
    removeCurrentQuestion();
}



function insertSurvivedQuestion($question){
    global $DB, $USER;
    $DB->insert_record('block_oa_survived', array('user'=>$USER->id, 'question' => $question));


}


function insertCurrentQuestion($questionid){
    global $DB, $USER;
    $DB->insert_record('block_oa_current', array('user'=>$USER->id, 'question' => $questionid, 'time' => time()));
}

function removeCurrentQuestion(){
    global $DB, $USER;
    $conditions = array('user'=>$USER->id);
    $DB->delete_records('block_oa_current', $conditions);

}

function endSurvivalStreak(){

    //get count from survived
    //insert into streak if new record
    //delete survived

}

function getQuestionSurvival(){
    global $DB;

    if($row = getCurrentQuestionId()){
        $id = $row->question;
    }
    else{

    $catid = 13;
    $questions = getQuestionsFromCategory($catid, $DB);
        if(!$questions){
            //there are no questions left
            endSurvivalStreak();
            return false;
        }

    $question_ids = [];
    foreach ($questions as $key => $question) {
        $question_ids[] = $key;
    }
    $rand = rand(0, count($question_ids) - 1);
    $id = $question_ids[$rand];

    insertCurrentQuestion($id);
}
    return question_bank::load_question($id);
}

function increaseUsersPoints(){
    global $USER;
    global $DB;
    $UserId = $USER->id;
    $diff = 5;
    $userPoints = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$userPoints){
        $fieldId = createNewUser($UserId,$DB);
        $points=0;
    }
    else{
        $points=$userPoints->points;
        $fieldId = $userPoints->id;
    }

    $DB->update_record('block_oa_users', array('id'=>$fieldId, 'points' => $points+$diff));
    return $diff;
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
    //$table = 'question';
    global $USER;
    $conditions = array('category'=>$catId);
    $sort = null;
    $fields = 'id';

    $result = $DB->get_records_sql('SELECT id FROM {question} AS q WHERE category = ? AND NOT EXISTS
        (SELECT * FROM {block_oa_survived} AS oa WHERE oa.user = ? AND oa.question = q.id)',
        array($catId, $USER->id));
  //  $result = $DB->get_records($table,$conditions,$sort,$fields);
    return $result;
}