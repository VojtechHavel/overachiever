<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */
defined('MOODLE_INTERNAL') || die();

class overachiever_form extends block_edit_form {

    protected function specific_definition($mform) {

        //form for description and title of the form
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_overachiever'));
        $mform->setDefault('config_title', get_string('overachiever', 'block_overachiever'));
        $mform->setType('config_title', PARAM_TEXT);
        $mform->addElement('textarea', 'config_description', get_string('blockdescription', 'block_overachiever'));
        $mform->setDefault('config_description', get_string('defaultdescription', 'block_overachiever'));
        $mform->setType('config_description', PARAM_TEXT);
    }
}