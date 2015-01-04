<?php
/**
 * Created by Vojtěch Havel on 2014/12/25
 */

require('../../config.php');
//require_once(dirname(__FILE__) . '/../../config.php');

require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT;


if ($courseid = optional_param('courseid', false, PARAM_INT)) {
}
else {
    $courseid = $COURSE->id;
}

$finalPage = showWithLayoutFirst('help.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;
echo $OUTPUT->heading(get_string('help', 'block_overachiever'));

echo html_writer::start_tag('h4');
echo get_string('aboutgame', 'block_overachiever');
echo html_writer::end_tag('h4');

echo get_string('aboutgametext', 'block_overachiever');

echo html_writer::start_tag('h4');
echo get_string('survival', 'block_overachiever');
echo html_writer::end_tag('h4');

echo get_string('survivaltext', 'block_overachiever');

echo html_writer::start_tag('h4');
echo get_string('feedback', 'block_overachiever');
echo html_writer::end_tag('h4');

echo get_string('feedbacktext', 'block_overachiever');
echo '<br>';
echo '<br>';
$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->footer();