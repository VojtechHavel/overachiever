<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */

require_once('../../config.php');
require_once('model.php');

global $DB, $COURSE, $PAGE, $OUTPUT, $USER;

//require logged in regular user
if (!$USER->id || isguestuser()){
    redirect('../../');
}

//set context
if ($courseid = optional_param('courseid', false, PARAM_INT)) {
}
else {
    $courseid = $COURSE->id;
}

$course = $DB->get_record('course', array('id' => $courseid));
$context = context_course::instance($courseid);
require_login($course);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/overachiever/profile.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');

$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);
$PAGE->set_heading($block_overachiever->config->title);

echo $OUTPUT->header() . '<link href="style.css" rel="stylesheet">';

//get data
$streakrecord = getRecordStreak();
$points = getUsersPoints($USER->id,$DB);
$qanswered = getUsersQAnswered($USER->id,$DB);
$qcorrect = getUsersQCorrect($USER->id,$DB);

echo $OUTPUT->heading(get_string('profile', 'block_overachiever'));

//prifile picture
echo '<div class="profilepicrightfill"></div>';
$profilePic =  '<div class="userprofilebox clearfix"><div class="profilepicture">';
$profilePic.=  $OUTPUT->user_picture($USER, array('size' => 100));
$profilePic.= '</div>';
echo $profilePic;
echo html_writer::start_tag('dl', array('class' => 'list'));

//points
echo html_writer::tag('dt', get_string('points', 'block_overachiever'));
echo html_writer::tag('dd', $points);

//record streak
echo html_writer::tag('dt', get_string('streakrecord','block_overachiever'));
echo html_writer::tag('dd', $streakrecord);

//all questions answered
echo html_writer::tag('dt', get_string('qanswered','block_overachiever'));
echo html_writer::tag('dd', $qanswered);

//number of questions answered correctly
echo html_writer::tag('dt', get_string('qcorrect','block_overachiever'));
echo html_writer::tag('dd', $qcorrect);

echo html_writer::end_tag('dl');
echo '</div>';

//home button
$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'menu', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->footer();