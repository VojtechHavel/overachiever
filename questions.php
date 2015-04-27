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


    $finalPage = showWithLayoutFirst('badges.php', $DB, $COURSE, $PAGE, $OUTPUT);
    echo $finalPage;
    echo $OUTPUT->heading(get_string('badgemanage', 'block_overachiever'));
    // require_capability('moodle/badges:createbadge', $PAGE->context);

    require_once('classes/badge_form.php');
    $mform = new badge_add_form();
    $mformdel = new badge_delete_form();

    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form
    } else if ($fromform = $mform->get_data()) {

        addBadge($fromform->badgetype,$fromform->param,$fromform->badges);
        echo get_string('badgeadded', 'block_overachiever');
        //In this case you process validated data. $mform->get_data() returns data posted in form.
    }
    else if ($fromform = $mformdel->get_data()) {

        deleteBadge($fromform->badges);
        echo get_string('badgedeleted', 'block_overachiever');
        //In this case you process validated data. $mform->get_data() returns data posted in form.
    }
    else {
        // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.

        //Set default data (if any)
        //$mform->set_data("");
        //displays the form

        //adding badges
        echo html_writer::start_tag('h3');
        echo get_string('badgeadd', 'block_overachiever');
        echo html_writer::end_tag('h3');
        echo get_string('badgefirstnewurl', 'block_overachiever').'<a href="'.new moodle_url("/badges/newbadge.php?type=1").'">'.get_string('here', 'block_overachiever').'</a>';

        if(getAllBadgesCreatedByUser()) {
            echo html_writer::start_tag('h5');
            echo get_string('badgeaddnewoa', 'block_overachiever');
            echo html_writer::end_tag('h5');

            $mform->display();
        }
        if(getOABadgesAddedByUser()) {
            //deleting badges
            echo html_writer::start_tag('h3');
            echo get_string('badgedelete', 'block_overachiever');
            echo html_writer::end_tag('h3');

            $mformdel->display();
        }
    }





    //menu button
    $homeurl = 'menu.php';
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
    echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'again', 'value' => get_string('menu', 'block_overachiever')));
    echo html_writer::end_tag('form');

    echo $OUTPUT->footer();

};