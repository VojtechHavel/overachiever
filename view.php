<?php
/* *
 * @package    mod_quizit
 * @copyright  2014 Vojtěch Havel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot . '/mod/quizit/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // quizit instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('quizit', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $quizit  = $DB->get_record('quizit', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $quizit  = $DB->get_record('quizit', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $quizit->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('quizit', $quizit->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

/// Print the page header

$PAGE->set_url('/mod/quizit/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($quizit->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

if ($quizit->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('quizit', $quizit, $cm->id), 'generalbox mod_introbox', 'quizitintro');
}

echo $OUTPUT->heading(get_string('modulename', 'mod_quizit'));

// game here

echo '<link href="style.css" rel="stylesheet">';
echo quizit_addgame($context, $course);

// Finish the page
echo $OUTPUT->footer();
?>