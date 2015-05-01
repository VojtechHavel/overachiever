<?php
/**
 * Created by Vojtěch Havel on 2015/04/27
 */

require('../../config.php');
require_once('model.php');
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT;

if(!canAdd()){
    redirect('menu.php');
}
else {

    if ($courseid = optional_param('courseid', false, PARAM_INT)) {
    } else {
        $courseid = $COURSE->id;
    }


    $finalPage = showWithLayoutFirst('questions.php', $DB, $COURSE, $PAGE, $OUTPUT);
    echo $finalPage;
    echo $OUTPUT->heading(get_string('questionmanage', 'block_overachiever'));

    require_once('classes/question_form.php');
    $mform = new question_add_form();
    $mformdel = new question_delete_form();

    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form
    } else if ($fromform = $mform->get_data()) {
        echo var_dump($fromform);
        //addQuestion($fromform->badgetype,$fromform->param,$fromform->badges);
        echo get_string('questionadded', 'block_overachiever');
        //In this case you process validated data. $mform->get_data() returns data posted in form.
    }
    else if ($fromform = $mformdel->get_data()) {
        echo var_dump($fromform);
        //deleteQuestion($fromform->question);
        echo get_string('questiondeleted', 'block_overachiever');
        //In this case you process validated data. $mform->get_data() returns data posted in form.
    }
    else {
        // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.

        //Set default data (if any)
        //$mform->set_data("");
        //displays the form

        //adding questions
            if (getAllQuestionsCreatedByUser()) {
                echo html_writer::start_tag('h3');
                echo get_string('questionaddnewoa', 'block_overachiever');
                echo html_writer::end_tag('h3');

                $mform->display();
            }

        if(getOABadgesAddedByUser()) {
            //deleting questions
            echo html_writer::start_tag('h3');
            echo get_string('questiondelete', 'block_overachiever');
            echo html_writer::end_tag('h3');

            $mformdel->display();
        }

        elseif(!getOABadgesAddedByUser()&&!getAllQuestionsCreatedByUser()){
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