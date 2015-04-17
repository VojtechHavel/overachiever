<?php
/**
 * Created by Vojtěch Havel on 2015/04/17
 */

require_once("$CFG->libdir/formslib.php");

class feedback_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('textarea', 'feedbackText', '', 'wrap="virtual" rows="10" cols="80"');
        $mform->addRule('feedbackText', get_string('feedbackmessageempty', 'block_overachiever'), 'required');
        $mform->addElement('submit', 'submitbutton', get_string('send', 'block_overachiever'));
    }
}