<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of quizit
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/quizit
 */

/// (Replace quizit with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // quizit instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('quizit', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $quizit = get_record('quizit', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $quizit = get_record('quizit', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $quizit->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('quizit', $quizit->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "quizit", "view", "view.php?id=$cm->id", "$quizit->id");

/// Print the page header
$strquizits = get_string('modulenameplural', 'quizit');
$strquizit  = get_string('modulename', 'quizit');

$navlinks = array();
$navlinks[] = array('name' => $strquizits, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($quizit->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

print_header_simple(format_string($quizit->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strquizit), navmenu($course, $cm));

/// Print the main part of the page

echo 'YOUR CODE GOES HERE';


/// Finish the page
print_footer($course);

?>
