<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */


require_once('../../config.php');
require_once('menu.php');

global $DB, $COURSE, $USER;

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
    $PAGE->set_url('/blocks/overachiever/view.php', array('id' => $courseid));

$PAGE->set_pagelayout('standard');

$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);


$PAGE->set_heading($block_overachiever->config->title);


echo $OUTPUT->header();
echo '<link href="style.css" rel="stylesheet">';
echo overachiever_showmenu($USER->id,$DB);
echo $OUTPUT->footer();


?>