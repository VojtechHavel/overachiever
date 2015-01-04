<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */
defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/formslib.php");

class overachiever_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        // add group for text areas
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_overachiever'));

// add page title element.
        $mform->addElement('text', 'pagetitle', get_string('pagetitle', 'block_overachiever'));
        $mform->addRule('pagetitle', null, 'required', null, 'client');

// add display text field
        $mform->addElement('htmleditor', 'displaytext', get_string('displayedhtml', 'block_overachiever'));
        $mform->setType('displaytexttext', PARAM_RAW);
        $mform->addRule('displaytext', null, 'required', null, 'client');

        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');

        $this->add_action_buttons();
    }
}