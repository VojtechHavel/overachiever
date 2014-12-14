<?php
//TODO delete this file
/**
 * Created by Vojtěch Havel on 2014/12/13
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/../../question/previewlib.php');

global $DB;
/**
 * The maximum number of variants previewable. If there are more variants than this for a question
 * then we only allow the selection of the first x variants.
 * @var integer
 */
define('QUESTION_PREVIEW_MAX_VARIANTS', 100);

// Get and validate question id.
//$id = required_param('id', PARAM_INT);
$id = 2;
$question = question_bank::load_question($id);

function question_survival_url($questionid, $qubaid, question_preview_options $options,$context = null) {

    $params = array('id' => $questionid,
                    'survivalid' => $qubaid,);

    if (is_null($context)) {
        global $PAGE;
        $context = $PAGE->context;
    }
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }

    return new moodle_url('/blocks/overachiever/survival.php', $params);
}

require_login();
$category = $DB->get_record('question_categories',
    array('id' => $question->category), '*', MUST_EXIST);
$context = context::instance_by_id($category->contextid);
$PAGE->set_context($context);
$PAGE->set_url(question_survival_url($id, $quba->get_id(), $options, $context));
$PAGE->set_pagelayout('standard');
// Note that in the other cases, require_login will set the correct page context.

// Get and validate existing preview, or start a new one.
$survivalid = optional_param('previewid', 0, PARAM_INT);

$options = new question_preview_options($question);
$options->load_user_defaults();
$options->set_from_request();


if ($survivalid) {
    try {
        $quba = question_engine::load_questions_usage_by_activity($survivalid);

    } catch (Exception $e) {
        // This may not seem like the right error message to display, but
        // actually from the user point of view, it makes sense.
        print_error('submissionoutofsequencefriendlymessage', 'question',
            question_preview_url($question->id, $options->behaviour,
                $options->maxmark, $options, $options->variant, $context), null, $e);
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

    $maxvariant = 2;
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
$options->behaviour = $quba->get_preferred_behaviour();
$options->maxmark = $quba->get_question_max_mark($slot);

// Prepare a URL that is used in various places.
function action_url($qid,$aid){

return '?qid='.$qid;

}

$actionurl = action_url($question->id, $quba->get_id());

// Process any actions from the buttons at the bottom of the form.
if (data_submitted() && confirm_sesskey()) {

    try {

        if (optional_param('restart', false, PARAM_BOOL)) {
            restart_preview($previewid, $question->id, $options, $context);

        } else if (optional_param('fill', null, PARAM_BOOL)) {
            $correctresponse = $quba->get_correct_response($slot);
            if (!is_null($correctresponse)) {
                $quba->process_action($slot, $correctresponse);

                $transaction = $DB->start_delegated_transaction();
                question_engine::save_questions_usage_by_activity($quba);
                $transaction->allow_commit();
            }
            redirect($actionurl);

        } else if (optional_param('finish', null, PARAM_BOOL)) {
            $quba->process_all_actions();
            $quba->finish_all_questions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();
            redirect($actionurl);

        } else {
            $quba->process_all_actions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();

            $scrollpos = optional_param('scrollpos', '', PARAM_RAW);
            if ($scrollpos !== '') {
                $actionurl->param('scrollpos', (int) $scrollpos);
            }
            redirect($actionurl);
        }

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




//$tet = get_string('previewquestion', 'question', format_string($question->name));
//$PAGE->set_heading($block_overachiever->config->title);
$instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
$block_overachiever = block_instance('overachiever', $instance);

$title = $block_overachiever->config->title;
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($block_overachiever->config->title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
$OUTPUT->heading('Hi');
//$OUTPUT->heading(format_string($question->name));
// Start output.

// Start the question form.
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
    'enctype' => 'multipart/form-data', 'id' => 'responseform'));
echo html_writer::start_tag('div');
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots', 'value' => $slot));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scrollpos', 'value' => '', 'id' => 'scrollpos'));
echo html_writer::end_tag('div');

// Output the question.
$options->feedback = question_display_options::HIDDEN;
$options->correctness = question_display_options::HIDDEN;
$options->flags = question_display_options::HIDDEN;
$options->numpartscorrect = question_display_options::HIDDEN;
$options->generalfeedback = question_display_options::HIDDEN;
$options->rightanswer = question_display_options::HIDDEN;
$options->manualcomment = question_display_options::HIDDEN;




echo $quba->render_question($slot, $options, null);

// Finish the question form.
//echo html_writer::start_tag('div', array('id' => 'previewcontrols', 'class' => 'controls'));
//echo html_writer::empty_tag('input', $restartdisabled + array('type' => 'submit',
//        'name' => 'restart', 'value' => get_string('restart', 'question')));
//echo html_writer::empty_tag('input', $finishdisabled  + array('type' => 'submit',
//        'name' => 'save',    'value' => get_string('save', 'question')));
//echo html_writer::empty_tag('input', $filldisabled    + array('type' => 'submit',
//        'name' => 'fill',    'value' => get_string('fillincorrect', 'question')));
//echo html_writer::empty_tag('input', $finishdisabled  + array('type' => 'submit',
//        'name' => 'finish',  'value' => get_string('submitandfinish', 'question')));
//echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

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