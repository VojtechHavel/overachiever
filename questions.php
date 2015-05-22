<?php
/**
 * Created by Vojtěch Havel on 2015/04/27
 */

require('../../config.php');
require_once('model.php');
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT;

//if user can create questions in Moodle, that he can add them to the game
//or else he will be redirected to menu
if (!canAdd()) {
    redirect('menu.php');
} else {
    //show first part of layout
    if ($courseid = optional_param('courseid', false, PARAM_INT)) {
    } else {
        $courseid = $COURSE->id;
    }

    $finalPage = showWithLayoutFirst('questions.php', $DB, $COURSE, $PAGE, $OUTPUT);
    echo $finalPage;
    echo $OUTPUT->heading(get_string('questionmanage', 'block_overachiever'));

    require_once('forms/question_form.php');

    //create forms for adding/deleting question to/from game
    $mform = new question_add_form();
    $mformdel = new question_delete_form();

    // cancellation of form
    if ($mform->is_cancelled()) {
    }
    //adding question
    else if ($fromform = $mform->get_data()) {
        addQuestion($fromform->question);
        echo get_string('questionadded', 'block_overachiever');
        echo '<br>';

        //back button
        $homeurl = 'questions.php';
        echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'back', 'value' => get_string('backquestion', 'block_overachiever')));
        echo html_writer::end_tag('form');
    //removing question
    } else if ($fromform = $mformdel->get_data()) {
        deleteQuestion($fromform->question);
        echo get_string('questiondeleted', 'block_overachiever');
        echo '<br>';

        //back button
        $homeurl = 'questions.php';
        echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'back', 'value' => get_string('backquestion', 'block_overachiever')));
        echo html_writer::end_tag('form');
        //form shown for first time or displazed again
    } else {
        //adding questions (if user created any in the Moodle)
        if (getAllQuestionsCreatedByUser(false)) {
            echo html_writer::start_tag('h3');
            echo get_string('questionaddnewoa', 'block_overachiever');
            echo html_writer::end_tag('h3');

            $mform->display();
        }

        //deleting questions (if user added anz into the game)
        if (getOAQuestionsAddedByUser()) {
            echo html_writer::start_tag('h3');
            echo get_string('questiondelete', 'block_overachiever');
            echo html_writer::end_tag('h3');

            $mformdel->display();
        } elseif (!getOAQuestionsAddedByUser() && !getAllQuestionsCreatedByUser(false)) {
            echo get_string('questioncreatefirst', 'block_overachiever');
        }
    }


    //menu button
    $homeurl = 'menu.php';
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
    echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('menu', 'block_overachiever')));
    echo html_writer::end_tag('form');

    echo $OUTPUT->footer();

};