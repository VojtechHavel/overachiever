<?php
/**
 * Created by Vojtěch Havel on 2015/04/17
 */

require_once('../../config.php');
require_once('model.php');

global $DB, $COURSE, $PAGE, $OUTPUT, $USER;


if ($courseid = optional_param('courseid', false, PARAM_INT)) {
}
else {
    $courseid = $COURSE->id;
}

$course = $DB->get_record('course', array('id' => $courseid));
$context = context_course::instance($courseid);
//require_login($course);

$PAGE->set_context($context);
//    $PAGE->set_pagelayout('course');
$PAGE->set_url('/blocks/overachiever/profile.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');

$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);
$PAGE->set_heading($block_overachiever->config->title);

echo $OUTPUT->header() . '<link href="style.css" rel="stylesheet">';

$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'menu', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->heading(get_string('feedbackheader', 'block_overachiever'));

//include simplehtml_form.php
require_once('classes/feedback_form.php');

//Instantiate simplehtml_form
$mform = new feedback_form();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {

    $headers = 'From: users@zabavnabiologie.cz';
    $sent = mail("zaboduj@zabavnabiologie.cz", "feedback from ".$USER->username, $mform->get_data()->feedbackText, $headers );
    if($newBadgeText = badgeUtils::getBadgePopup($USER->id, 0)) {
        echo $newBadgeText;
    }
    echo get_string('feedbackthanks', 'block_overachiever');
    //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    //Set default data (if any)
    $mform->set_data("");
    //displays the form
    $mform->display();
}

echo $OUTPUT->footer();