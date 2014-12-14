<?php
/**
 * Created by Vojtěch Havel on 2014/12/14
 */
//TODO delete commented code from another files
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/../../question/previewlib.php');
require_once('questionlib.php');
require_once('model.php');
/**
 * The maximum number of variants previewable. If there are more variants than this for a question
 * then we only allow the selection of the first x variants.
 * @var integer
 */
define('QUESTION_PREVIEW_MAX_VARIANTS', 100);
global $DB;
// Get and validate question id.
//$id = required_param('id', PARAM_INT);
$catid = 13;
$questions = getQuestionsFromCategory($catid,$DB);



$question_ids = [];
foreach ($questions as $key => $question) {
    $question_ids[] = $key;
}
$id = $question_ids[1];


$question = question_bank::load_question($id);

// Were we given a particular context to run the question in?
// This affects things like filter settings, or forced theme or language.
if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $context = context_module::instance($cmid);
    $PAGE->set_pagelayout('standard');

} else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    require_login($courseid);
    $context = context_course::instance($courseid);
    $PAGE->set_pagelayout('standard');

} else {
    require_login();
    $category = $DB->get_record('question_categories',
        array('id' => $question->category), '*', MUST_EXIST);
    $context = context::instance_by_id($category->contextid);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    // Note that in the other cases, require_login will set the correct page context.
}

//question_require_capability_on($question, 'use');

// Get and validate display options.
$maxvariant = min($question->get_num_variants(), QUESTION_PREVIEW_MAX_VARIANTS);
$options = new question_preview_options($question);
$options->load_user_defaults();
$options->set_from_request();
$options->behaviour = 'immediatefeedback';
$PAGE->set_url(question_general_url($id, $options->behaviour, $options->maxmark,
    $options, $options->variant, $context,'/blocks/overachiever/survival.php'));

// Get and validate existing preview, or start a new one.
$previewid = optional_param('previewid', 0, PARAM_INT);

if ($previewid) {
    try {
        $quba = question_engine::load_questions_usage_by_activity($previewid);

    } catch (Exception $e) {
        // This may not seem like the right error message to display, but
        // actually from the user point of view, it makes sense.
        print_error('submissionoutofsequencefriendlymessage', 'question',
            question_general_url($question->id, $options->behaviour,
                $options->maxmark, $options, $options->variant, $context, '/blocks/overachiever/survival.php'), null, $e);
    }

    if ($quba->get_owning_context()->instanceid != $USER->id) {
        print_error('notyourpreview', 'question');
    }

    $slot = $quba->get_first_question_number();
    $usedquestion = $quba->get_question($slot);
    if ($usedquestion->id != $question->id) {
        print_error('questionidmismatch', 'question');
    }
    $question = $usedquestion;
    $options->variant = $quba->get_variant($slot);

} else {
    $quba = question_engine::make_questions_usage_by_activity(
        'core_question_preview', context_user::instance($USER->id));
    $quba->set_preferred_behaviour($options->behaviour);
    $slot = $quba->add_question($question, $options->maxmark);

    if ($options->variant) {
        $options->variant = min($maxvariant, max(1, $options->variant));
    } else {
        $options->variant = rand(1, $maxvariant);
    }

    $quba->start_question($slot, $options->variant);

    $transaction = $DB->start_delegated_transaction();
    question_engine::save_questions_usage_by_activity($quba);
    $transaction->allow_commit();
}

//$options->behaviour = $quba->get_preferred_behaviour();
//$options->maxmark = $quba->get_question_max_mark($slot);
//
//// Create the settings form, and initialise the fields.
//$optionsform = new preview_options_form(question_preview_form_url($question->id, $context, $previewid),
//    array('quba' => $quba, 'maxvariant' => $maxvariant));
//$optionsform->set_data($options);
//
//// Process change of settings, if that was requested.
//if ($newoptions = $optionsform->get_submitted_data()) {
//    // Set user preferences.
//    $options->save_user_preview_options($newoptions);
//    if (!isset($newoptions->variant)) {
//        $newoptions->variant = $options->variant;
//    }
//    if (isset($newoptions->saverestart)) {
//        restart_preview($previewid, $question->id, $newoptions, $context);
//    }
//}

// Prepare a URL that is used in various places.
$actionurl = question_general_action_url($question->id, $quba->get_id(), $options, $context, '/blocks/overachiever/survival.php');

// Process any actions from the buttons at the bottom of the form.
if (data_submitted() && confirm_sesskey()) {

    try {

//        if (optional_param('restart', false, PARAM_BOOL)) {
//            restart_preview($previewid, $question->id, $options, $context);
//
//        } else if (optional_param('fill', null, PARAM_BOOL)) {
//            $correctresponse = $quba->get_correct_response($slot);
//            if (!is_null($correctresponse)) {
//                $quba->process_action($slot, $correctresponse);
//
//                $transaction = $DB->start_delegated_transaction();
//                question_engine::save_questions_usage_by_activity($quba);
//                $transaction->allow_commit();
//            }
//            redirect($actionurl);
//
//        } else if (optional_param('finish', null, PARAM_BOOL)) {
//            $quba->process_all_actions();
//            $quba->finish_all_questions();
//
//            $transaction = $DB->start_delegated_transaction();
//            question_engine::save_questions_usage_by_activity($quba);
//            $transaction->allow_commit();
//            redirect($actionurl);
//
//        } else {
            $quba->process_all_actions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();

            $scrollpos = optional_param('scrollpos', '', PARAM_RAW);
            if ($scrollpos !== '') {
                $actionurl->param('scrollpos', (int) $scrollpos);
            }
            redirect($actionurl);
//        }

    } catch (question_out_of_sequence_exception $e) {
        print_error('submissionoutofsequencefriendlymessage', 'question', $actionurl);

    } catch (Exception $e) {
        // This sucks, if we display our own custom error message, there is no way
        // to display the original stack trace.
        $debuginfo = '';
        if (!empty($e->debuginfo)) {
            $debuginfo = $e->debuginfo;
        }
        print_error('errorprocessingresponses', 'question', $actionurl,
            $e->getMessage(), $debuginfo);
    }
}

if ($question->length) {
    $displaynumber = '1';
} else {
    $displaynumber = 'i';
}
//$restartdisabled = array();
//$finishdisabled = array();
//$filldisabled = array();
//if ($quba->get_question_state($slot)->is_finished()) {
//    $finishdisabled = array('disabled' => 'disabled');
//    $filldisabled = array('disabled' => 'disabled');
//}
//// If question type cannot give us a correct response, disable this button.
//if (is_null($quba->get_correct_response($slot))) {
//    $filldisabled = array('disabled' => 'disabled');
//}
//if (!$previewid) {
//    $restartdisabled = array('disabled' => 'disabled');
//}

//// Prepare technical info to be output.
//$qa = $quba->get_question_attempt($slot);
//$technical = array();
//$technical[] = get_string('behaviourbeingused', 'question',
//    question_engine::get_behaviour_name($qa->get_behaviour_name()));
//$technical[] = get_string('technicalinfominfraction',     'question', $qa->get_min_fraction());
//$technical[] = get_string('technicalinfomaxfraction',     'question', $qa->get_max_fraction());
//$technical[] = get_string('technicalinfoquestionsummary', 'question', s($qa->get_question_summary()));
//$technical[] = get_string('technicalinforightsummary',    'question', s($qa->get_right_answer_summary()));
//$technical[] = get_string('technicalinfostate',           'question', '' . $qa->get_state());

// Start output.
$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);
$title = $block_overachiever->config->title;
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Start the question form.
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
    'enctype' => 'multipart/form-data', 'id' => 'responseform'));
echo html_writer::start_tag('div');
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots', 'value' => $slot));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scrollpos', 'value' => '', 'id' => 'scrollpos'));
echo html_writer::end_tag('div');

// Output the question.
echo $quba->render_question($slot, $options, $displaynumber);
echo $quba->get_question_fraction($slot);
//echo $quba->responsesummary;
//// Finish the question form.
//echo html_writer::start_tag('div', array('id' => 'previewcontrols', 'class' => 'controls'));
//echo html_writer::empty_tag('input', $restartdisabled + array('type' => 'submit',
//        'name' => 'restart', 'value' => get_string('restart', 'question')));
//echo html_writer::empty_tag('input', array('type' => 'submit',
//       'name' => 'save',    'value' => get_string('save', 'question')));
//echo html_writer::empty_tag('input', $filldisabled    + array('type' => 'submit',
//        'name' => 'fill',    'value' => get_string('fillincorrect', 'question')));
//echo html_writer::empty_tag('input', $finishdisabled  + array('type' => 'submit',
//        'name' => 'finish',  'value' => get_string('submitandfinish', 'question')));
//echo html_writer::end_tag('div');
//echo html_writer::end_tag('form');

//// Output the technical info.
//print_collapsible_region_start('', 'techinfo', get_string('technicalinfo', 'question') .
//    $OUTPUT->help_icon('technicalinfo', 'question'),
//    'core_question_preview_techinfo_collapsed', true);
//foreach ($technical as $info) {
//    echo html_writer::tag('p', $info, array('class' => 'notifytiny'));
//}
//print_collapsible_region_end();

// Display the settings form.
//$optionsform->display();

$PAGE->requires->js_module('core_question_engine');
$PAGE->requires->strings_for_js(array(
    'closepreview',
), 'question');
$PAGE->requires->yui_module('moodle-question-preview', 'M.question.preview.init');
echo $OUTPUT->footer();

echo '<script>      var divinfo = document.getElementsByClassName("info")[0];
                    divinfo.style.visibility="hidden";
                    divinfo.style.display="none";
                    divinfo.removeClass("info");
                 /*   var divcont = document.getElementsByClassName("content")[0];
                    divcont.style.marginleft="0px";*/
                    </script>';
echo '<style media="screen" type="text/css">
.que .content {
margin: 0px 0 0 0em;
margin-top: 0px;
margin-right: 0px;
margin-bottom: 0px;
margin-left: 0em;
}
</style>';
