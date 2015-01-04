<?php
/**
 * Created by Vojtěch Havel on 2014/12/25
 */


require('../../config.php');
//require_once(dirname(__FILE__) . '/../../config.php');
require "$CFG->libdir/tablelib.php";
require "classes/ladder_table.php";

require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT;


if ($courseid = optional_param('courseid', false, PARAM_INT)) {
}
else {
    $courseid = $COURSE->id;
}


$table = new ladder_table('uniqueid',$DB);



// Work out the sql for the table.
//$table->set_sql('*', "{user}", '1');

$table->define_baseurl("$CFG->wwwroot/blocks/overachiever/ladder.php");

$finalPage = showWithLayoutFirst('ladder.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;
$table->out(20, true);

$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->footer();