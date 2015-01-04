<?php
/**
 * Created by Vojtěch Havel on 2014/12/13
 */

/**
 * @param $blockContent content of page
 * @param $pageUrl e.g. random.php
 * @param $DB
 * @param $COURSE
 * @param $PAGE
 * @param $OUTPUT
 * @return string returns whole page with $blockContent as a content
 */
defined('MOODLE_INTERNAL') || die();
function showWithLayout($blockContent, $pageUrl, $DB, $COURSE, $PAGE, $OUTPUT)
{

    if ($courseid = optional_param('courseid', false, PARAM_INT)) {
    }
    else {
        $courseid = $COURSE->id;
    }

    $course = $DB->get_record('course', array('id' => $courseid));
    $context = context_course::instance($courseid);
    require_login($course);

    $PAGE->set_context($context);
//    $PAGE->set_pagelayout('course');
    $PAGE->set_url('/blocks/overachiever/' . $pageUrl, array('id' => $courseid));
    $PAGE->set_pagelayout('standard');

    $instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
    $block_overachiever = block_instance('overachiever', $instance);
    $PAGE->set_heading($block_overachiever->config->title);

    return $OUTPUT->header() . '<link href="style.css" rel="stylesheet">'.$blockContent.$OUTPUT->footer();
}

function showWithLayoutfirst($pageUrl, $DB, $COURSE, $PAGE, $OUTPUT)
{

    if ($courseid = optional_param('courseid', false, PARAM_INT)) {
    }
    else {
        $courseid = $COURSE->id;
    }

    $course = $DB->get_record('course', array('id' => $courseid));
    $context = context_course::instance($courseid);
    require_login($course);

    $PAGE->set_context($context);
//    $PAGE->set_pagelayout('course');
    $PAGE->set_url('/blocks/overachiever/' . $pageUrl, array('id' => $courseid));
    $PAGE->set_pagelayout('standard');

    $instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
    $block_overachiever = block_instance('overachiever', $instance);
    $PAGE->set_heading($block_overachiever->config->title);

    return $OUTPUT->header() . '<link href="style.css" rel="stylesheet">';
}