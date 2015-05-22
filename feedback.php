<?php
/**
 * Created by Vojtěch Havel on 2015/04/17
 */

require_once('../../config.php');
require_once('model.php');
require_once('forms/feedback_form.php');
global $DB, $COURSE, $PAGE, $OUTPUT, $USER;

//require logged in regular user
if (!$USER->id || isguestuser()){
    redirect('../../');
}

//set context of page
if ($courseid = optional_param('courseid', false, PARAM_INT)) {
}
else {
    $courseid = $COURSE->id;
}
$course = $DB->get_record('course', array('id' => $courseid));
$context = context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url('/blocks/overachiever/profile.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);
$PAGE->set_heading($block_overachiever->config->title);

echo $OUTPUT->header() . '<link href="style.css" rel="stylesheet">';

//menu button
$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'menu', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->heading(get_string('feedbackheader', 'block_overachiever'));

$mform = new feedback_form();

//form cancelled
if ($mform->is_cancelled()) {
}
//form submitted
else if ($fromform = $mform->get_data()) {
    //send email
    $headers = "Content-type: text/plain; charset=UTF-8\r\n";
    $headers .= 'From: users@zabavnabiologie.cz';
    $sent = mail("zaboduj@zabavnabiologie.cz", "feedback from ".$USER->username, $mform->get_data()->feedbackText, $headers );
    //display feedback badge if it was awarded
    if($newBadgeText = badgeUtils::getBadgePopup($USER->id, 0)) {
        echo $newBadgeText;
    }
    echo get_string('feedbackthanks', 'block_overachiever');
}
//first time or display again
else {
    $mform->set_data("");
    $mform->display();
}

echo $OUTPUT->footer();