<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
require('utility.php');

require('../../config.php');

global $DB, $COURSE, $PAGE, $OUTPUT;
//$data = $DB->get_record($table, array $conditions, $fields='*', $strictness=IGNORE_MISSING);

$user = $DB->get_record('user', array('id'=>'1'));

$text = $user->username;
$head = showWithLayout($text ,'random',$DB, $COURSE, $PAGE, $OUTPUT);


echo $head;


//
//
//require_once('utility.php');
//showWithLayout('svqvq' ,'random');