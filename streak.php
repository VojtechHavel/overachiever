<?php
/**
 * Created by Vojtěch Havel on 2014/12/14
 * modification of question/preview.php
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/../../question/previewlib.php');
//require_once(__DIR__.'/questionlib.php');
require_once(__DIR__.'/model.php');

define('QUESTION_PREVIEW_MAX_VARIANTS', 100);
global $DB, $USER;

    $question = getQuestionStreak();

if($question) {

    $courseid = $COURSE->id;
    $course = $DB->get_record('course', array('id' => $courseid));
    $context = context_course::instance($courseid);
    require_login($course);

    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');


// Display options - immediate feedback
    $maxvariant = min($question->get_num_variants(), QUESTION_PREVIEW_MAX_VARIANTS);
   $options = new question_preview_options($question);

    $options->behaviour = 'immediatefeedback';


    $PAGE->set_url('/blocks/overachiever/streak.php', array('id' => $courseid));



    $qid = optional_param('qid', 0, PARAM_INT);

    if ($qid) {
        try {
            $quba = question_engine::load_questions_usage_by_activity($qid);

        } catch (Exception $e) {
            print_error('submissionoutofsequencefriendlymessage', 'question',
                question_general_url($question->id, $options->behaviour,
                    $options->maxmark, $options, $options->variant, $context, '/blocks/overachiever/streak.php'), null, $e);
        }

        $slot = $quba->get_first_question_number();
        $usedquestion = $quba->get_question($slot);
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

    $actionurl = new moodle_url('/blocks/overachiever/streak.php', array('qid' => $quba->get_id(),));
    $reloadurl = new moodle_url('/blocks/overachiever/streak.php');

//Process form
    if (data_submitted() && confirm_sesskey()) {

        try {
        if (optional_param('again',false,PARAM_TEXT)) {

        } else {
            $quba->process_all_actions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();

            $params = array('fraction' => $quba->get_question_fraction($slot));
            $result = questionAnswered($params);
            $pointsinc = $result['pointsinc'];
            $actionurl->param('pointsinc', (int)$pointsinc);
            redirect($actionurl);
        }

        } catch (question_out_of_sequence_exception $e) {
            print_error('submissionoutofsequencefriendlymessage', 'question', $actionurl);

        } catch (Exception $e) {
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

// Start output
    $instance = $DB->get_record('block_instances', array('blockname' => 'overachiever'), '*', MUST_EXIST);
    $block_overachiever = block_instance('overachiever', $instance);
    $title = $block_overachiever->config->title;
    $headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    echo $OUTPUT->header();

    if($USER->id == 2){
        //admin cheat for correct answer
        echo "question id: ".$question->id;
        var_dump($quba->get_correct_response($slot));
    }


    /*fraction - 1 if answer is correct,
    (0-1) partial,
     0 wrong
    */
    $fraction = $quba->get_question_fraction($slot);
    if($fraction==1){
        questionSurvived($question->id, $fraction);
    }

    // Home button
    $homeurl = 'menu.php';
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
    echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'menu', 'value' => get_string('menu', 'block_overachiever')));
    echo html_writer::end_tag('form');

    //Streak
    echo html_writer::start_tag('div', array('class' => 'streak'));
    echo get_string('currentstreak', 'block_overachiever');
    echo getCurrentStreak();
    echo html_writer::end_tag('div');

// Question form
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
        'enctype' => 'multipart/form-data', 'id' => 'responseform'));
    echo html_writer::start_tag('div');
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots', 'value' => $slot));
    echo html_writer::end_tag('div');

// Set options of answer
$options->feedback = question_display_options::HIDDEN;
$options->rightanswer = question_display_options::VISIBLE;


    $question = $quba->render_question($slot, $options, $displaynumber);
    $index = strpos($question, 'rightanswer' );
    if($index){
        $index = strpos($question, '<br>', $index );
        while($index) {
            $question = substr_replace($question, '', $index, 4);
            $index = strpos($question, '<br>' , $index);
        }
    }



    $index = strpos($question, 'rightanswer' );
    if($index){
        $index = strpos($question, '<span', $index );
        while($index) {
            $question = substr_replace($question, '<br>', $index, 0);
            $index = strpos($question, '<span' , $index+5);
        }
    }
    echo $question;
    echo html_writer::end_tag('form');




    // Show results
    if ($fraction !== null) {
//if question was answered, there is fraction of answer correctness
//then show button for next question and points received

        if ($fraction == 1) {
            echo html_writer::start_tag('div', array('class' => 'myfeedback feedbackCorrect'));
            if ($pointsinc = optional_param('pointsinc', false, PARAM_INT)) {
                echo get_string('feedbackcorrectstart', 'block_overachiever');
                echo $pointsinc;
                echo get_string('feedbackcorrectend', 'block_overachiever');
            } else {
                echo get_string('feedbackcorrect', 'block_overachiever');
            }

            echo html_writer::end_tag('div');
        }
        else {
        if($fraction == 0) {
                echo html_writer::start_tag('div', array('class' => 'myfeedback feedbackWrong'));
                echo get_string('feedbackwrong', 'block_overachiever');
                echo html_writer::end_tag('div');
            } else {
                echo html_writer::start_tag('div', array('class' => 'myfeedback feedbackPartial'));
                echo get_string('feedbackpartial', 'block_overachiever');
                echo html_writer::end_tag('div');
            }

            if($newStreakRecord = endStreak()){
                echo html_writer::start_tag('div', array('class' => 'myfeedback feedbackCorrect'));
                echo get_string('newrecord', 'block_overachiever');
                echo $newStreakRecord;
                echo html_writer::end_tag('div');
            }
            }

        echo html_writer::start_tag('form', array('method' => 'post', 'action' => $reloadurl, 'id' => 'nextform'));
        echo html_writer::start_tag('div');
        if($fraction == 1) {
            echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'nextQ', 'value' => get_string('next', 'block_overachiever')));
        }
        else{
            echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('again', 'block_overachiever')));
        }
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    };




    $PAGE->requires->js_module('core_question_engine');
    $PAGE->requires->strings_for_js(array(
        'closepreview',
    ), 'question');
    $PAGE->requires->yui_module('moodle-question-preview', 'M.question.preview.init');
    echo $OUTPUT->footer();
    echo '<link href="style.css" rel="stylesheet">';
    if(true) {
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
    }
}
else{
    require_once('utility.php');
    //there is no question to display - all questions were used
    if($recordStreak = endStreak()) {
        //new record
        $blockContent = html_writer::start_tag('div', array('class' => 'myfeedback feedbackPartial'));
        $blockContent .= get_string('allqsanswered', 'block_overachiever');
        $blockContent .= html_writer::end_tag('div');

        $blockContent .= html_writer::start_tag('div', array('class' => 'myfeedback feedbackCorrect'));
        $blockContent .= get_string('newrecord', 'block_overachiever');
        $blockContent .=$recordStreak;
        $blockContent .= html_writer::end_tag('div');
    }
    else{
        //all questions anwered, but no new record
        $blockContent = html_writer::start_tag('div', array('class' => 'myfeedback feedbackPartial'));
        $blockContent .= get_string('allqsanswered', 'block_overachiever');
        $blockContent .= html_writer::end_tag('div');

    }
    $reloadurl = new moodle_url('/blocks/overachiever/streak.php');
    $blockContent .= html_writer::start_tag('form', array('method' => 'post', 'action' => $reloadurl, 'id' => 'againform'));
    $blockContent .=  html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('again', 'block_overachiever')));
    $blockContent .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
    $blockContent .= html_writer::end_tag('form');

    global $COURSE, $PAGE, $OUTPUT;
    $page = showWithLayout($blockContent, 'streak.php', $DB, $COURSE, $PAGE, $OUTPUT);
    echo $page;

}
