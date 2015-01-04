<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../config.php');

function getUsersPoints($UserId,$DB){
    $points = 0;
    $userPoints = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$userPoints){
        $points = 0;
    }
    elseif($userPoints->points){
        $points=$userPoints->points;
    }
    return $points;

}

function getUsersQAnswered($UserId,$DB){
    $points = 0;
    $userPoints = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$userPoints){
        $points = 0;
    }
    elseif($userPoints->qanswered){
        $points=$userPoints->qanswered;
    }
    return $points;

}

function getUsersQCorrect($UserId,$DB){
    $points = 0;
    $userPoints = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$userPoints){
        $points = 0;
    }
    elseif($userPoints->qcorrect){
        $points=$userPoints->qcorrect;
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

function increaseQAnswered(){
    global $USER;
    global $DB;
    $UserId = $USER->id;
    $user = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$user){
        $fieldId = createNewUser($UserId,$DB);
        $qanswered=0;
    }
    else{
        $qanswered=$user->qanswered;
        $fieldId = $user->id;
    }

    $DB->update_record('block_oa_users', array('id'=>$fieldId, 'qanswered' => $qanswered+1));
    return 1;
}

function increaseQCorrect(){
    global $USER;
    global $DB;
    $UserId = $USER->id;
    $user = $DB->get_record('block_oa_users', array('user'=>$UserId));
    if(!$user){
        $fieldId = createNewUser($UserId,$DB);
        $qcorrect=0;
    }
    else{
        $qcorrect=$user->qcorrect;
        $fieldId = $user->id;
    }

    $DB->update_record('block_oa_users', array('id'=>$fieldId, 'qcorrect' => $qcorrect+1));
    return 1;
}


function questionAnswered($params){

    increaseQAnswered();
    if($params['fraction']==1){
       increaseQCorrect();
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

function deleteSurvived(){
    global $DB, $USER;
    $conditions = array('user'=>$USER->id);
    $DB->delete_records('block_oa_survived', $conditions);

}

function getRecordStreakId(){
    global $DB, $USER;
    if($row = $DB->get_record('block_oa_streak', array('user'=>$USER->id))){
        return $row->id;
    }else{
        return 0;
    }
}

function insertStreakRecord($streak){
    global $DB, $USER;
    $fieldId = getRecordStreakId();
    if($fieldId != 0) {
        $DB->update_record('block_oa_streak', array('id' => $fieldId, 'user' => $USER->id, 'streak' => $streak, 'time' => time()));
    }
    else{
        $DB->insert_record('block_oa_streak', array('user' => $USER->id, 'streak' => $streak, 'time' => time()));
           }
}

function getCurrentStreak(){
    global $DB, $USER;
    if($row = $DB->count_records('block_oa_survived', array('user'=>$USER->id)) ){
            return $row;
    }else{
        return 0;
    };

}

function getRecordStreak(){
    global $DB, $USER;
    if($row = $DB->get_record('block_oa_streak', array('user'=>$USER->id))){
        if($row->streak) {
            return $row->streak;
        }
    }

        return 0;

}

function checkForStreakRecord(){
    $current = getCurrentStreak();
if($current>getRecordStreak()){
    return $current;
}
    else return false;
}

function endSurvivalStreak(){

    if($streak = checkForStreakRecord()){
       insertStreakRecord($streak);
        deleteSurvived();
        return $streak;
    };
    deleteSurvived();
    return false;
}

function getQuestionSurvival(){
    global $DB;

    if($row = getCurrentQuestionId()){
        $id = $row->question;
    }
    else{
    $catid = 13;
    $questions = getAvailableQuestions($DB);
        if(!$questions){
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
    $question = false;
    try{
        $question = question_bank::load_question($id);
    }
    catch(Exception $e){
        insertSurvivedQuestion($id);
        removeCurrentQuestion();
        $question = getQuestionSurvival();
        echo $id;
        return $question;
    }
    return $question;
}

function increaseUsersPoints(){
    global $USER;
    global $DB;
    $UserId = $USER->id;
    $diff = getCurrentStreak()+1;
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

//function getQuestionsFromCategory($catId, $DB){
//
//    global $USER;
//    $sort = null;
//
//    $result = $DB->get_records_sql('SELECT id FROM {question} AS q WHERE category = ? AND NOT EXISTS
//        (SELECT * FROM {block_oa_survived} AS oa WHERE oa.user = ? AND oa.question = q.id)',
//        array($catId, $USER->id));
//    return $result;
//}

function getAvailableQuestions($DB){
    global $USER;
    $sort = null;

    $result = $DB->get_records_sql('SELECT qid FROM {block_oa_questions} AS q WHERE NOT EXISTS
        (SELECT * FROM {block_oa_survived} AS oa WHERE oa.user = ? AND oa.question = q.qid)',
        array($USER->id));
    return $result;
}