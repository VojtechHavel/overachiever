<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */


function showWithLayout($blockContent,$pageUrl, $DB, $COURSE, $PAGE, $OUTPUT){

//require('../../config.php');
//
//
//global $DB, $COURSE, $PAGE, $OUTPUT;

// Check for all required variables.

//$courseid = required_param('courseid', PARAM_INT);
$courseid = $COURSE->id;

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    //print_error('invalidcourse', 'block_overachiever', $courseid);
}
else {

    $context = context_course::instance($courseid);

    require_login($course);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('course');
}
$PAGE->set_url('/blocks/overachiever/'.$pageUrl.'.php', array('id' => $courseid));

$PAGE->set_pagelayout('standard');

$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);


$PAGE->set_heading($block_overachiever->config->title);


return $OUTPUT->header().'<link href="style.css" rel="stylesheet">'.$blockContent.'con:'.$blockContent.$OUTPUT->footer();
//echo '<link href="style.css" rel="stylesheet">';
//echo $blockContent;
//echo $OUTPUT->footer();
}