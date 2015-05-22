<?php
/**
 * Created by Vojtěch Havel on 2014/12/25
 */


require(__DIR__ . '/../../config.php');
require "$CFG->libdir/tablelib.php";
require(__DIR__ . "/forms/ladder_table.php");
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT;

//require logged in regular user
if (!$USER->id || isguestuser()){
    redirect('../../');
}

$table = new ladder_table('uniqueid',$DB);
$table->define_baseurl("$CFG->wwwroot/blocks/overachiever/ladder.php");
$finalPage = showWithLayoutFirst('ladder.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;

// 20 rows on page
$table->out(20, true);

$homeurl = 'menu.php';
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('menu', 'block_overachiever')));
echo html_writer::end_tag('form');

echo $OUTPUT->footer();