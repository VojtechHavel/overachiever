<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */

require_once('../../config.php');
require_once('overachiever_form.php');
require_once('menu.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
//$courseid = required_param('courseid', PARAM_INT);

/*
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_overachiever', $courseid);
}*/

$blockid = required_param('blockid', PARAM_INT);

//$id = optional_param('id', 0, PARAM_INT);

require_login();
$PAGE->set_context(context_block::instance($blockid));
$PAGE->set_url('/blocks/overachiever/view.php', array('id' => $blockid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_overachiever'));

//$settingsnode = $PAGE->settingsnav->add(get_string('overachieversettings', 'block_overachiever'));
//$editurl = new moodle_url('/blocks/overachiever/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
//$editnode = $settingsnode->add(get_string('editpage', 'block_overachiever'), $editurl);
//$editnode->make_active();

////$overachiever = new overachiever_form();
//$toform['blockid'] = $blockid;
////$toform['courseid'] = $courseid;
//$overachiever->set_data($toform);
//
//if($overachiever->is_cancelled()) {
//    // Cancelled forms redirect to the course main page.
////    $courseurl = new moodle_url('/course/view.php', array('id' => $id));
//    redirect($courseurl);
//} else if ($fromform = $overachiever->get_data()) {
//    // We need to add code to appropriately act on and store the submitted data
//    // but for now we will just redirect back to the course main page.
//    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
//    //redirect($courseurl);
//   /* if (!$DB->insert_record('block_simplehtml', $fromform)) {
//        print_error('inserterror', 'block_simplehtml');
//    }*/
//
//    print_object($fromform);
//} else {
//    // form didn't validate or this is the first display
//    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->text =quizit_addgame();
    echo $OUTPUT->footer();
?>
