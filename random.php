<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
require('utility.php');
require('model.php');

require('../../config.php');

global $DB, $COURSE, $PAGE, $OUTPUT;
//$data = $DB->get_record($table, array $conditions, $fields='*', $strictness=IGNORE_MISSING);

$text = 'In progress';
$finalPage = showWithLayout($text ,'random.php',$DB, $COURSE, $PAGE, $OUTPUT);


echo $finalPage;


//
//
//require_once('utility.php');
//showWithLayout('svqvq' ,'random');