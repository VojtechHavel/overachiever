<?php
/**
 * Created by Vojtěch Havel on 2015/04/26
 */

require('../../config.php');
require_once('model.php');
require_once('utility.php');
require_once('forms/badge_form.php');

global $DB, $COURSE, $PAGE, $OUTPUT;

//if user can create badges in Moodle, that he can add them to the game
//or else he will be redirected to menu
if (!has_capability('moodle/badges:createbadge', context_system::instance(), $USER)) {
    redirect('menu.php');
} else {
    //show first part of layout
    $finalPage = showWithLayoutFirst('badges.php', $DB, $COURSE, $PAGE, $OUTPUT);
    echo $finalPage;
    echo $OUTPUT->heading(get_string('badgemanage', 'block_overachiever'));

    //create 2 forms - for adding and delting badges
    $mform = new badge_add_form();
    $mformdel = new badge_delete_form();

    //form cancelled
    if ($mform->is_cancelled()) {
    }
    //check if there are data from adding badge
    else if ($fromform = $mform->get_data()) {

        addBadge($fromform->badgetype, $fromform->param, $fromform->badges);
        echo get_string('badgeadded', 'block_overachiever');
        echo '<br>';

        //back button
        $homeurl = 'badges.php';
        echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'back', 'value' => get_string('backbadge', 'block_overachiever')));
        echo html_writer::end_tag('form');

    }
    //check if there are data from deleting badge
    else if ($fromform = $mformdel->get_data()) {
        deleteBadge($fromform->badges);
        echo get_string('badgedeleted', 'block_overachiever');
        echo '<br>';

        //back button
        $homeurl = 'badges.php';
        echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'back', 'value' => get_string('backbadge', 'block_overachiever')));
        echo html_writer::end_tag('form');
    }
    //form shown for the first time
    //OR data didn't validate - form has to be displayed again
    else {
        //adding badges
        echo html_writer::start_tag('h3');
        echo get_string('badgeadd', 'block_overachiever');
        echo html_writer::end_tag('h3');
        echo get_string('badgefirstnewurl', 'block_overachiever') . '<a href="' . new moodle_url("/badges/newbadge.php?type=1") . '">' . get_string('here', 'block_overachiever') . '</a>';

        //if user created any badges than he can add them to game
        if (getAllBadgesCreatedByUser()) {
            echo html_writer::start_tag('h5');
            echo get_string('badgeaddnewoa', 'block_overachiever');
            echo html_writer::end_tag('h5');

            //create form for adding badges to the game
            $mform->display();
        }

        //if user added any of his badges to game, than he can remove them from it
        if (getOABadgesAddedByUser()) {
            echo html_writer::start_tag('h3');
            echo get_string('badgedelete', 'block_overachiever');
            echo html_writer::end_tag('h3');

            //create form from removing badges from the game
            $mformdel->display();
        }
    }

    //menu button
    $homeurl = 'menu.php';
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => $homeurl));
    echo html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'menu', 'value' => get_string('menu', 'block_overachiever')));
    echo html_writer::end_tag('form');

    echo $OUTPUT->footer();

};