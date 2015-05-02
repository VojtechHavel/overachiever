<?php
/**
 * Created by Vojtěch Havel on 2015/04/27
 */

require_once("$CFG->libdir/formslib.php");
require_once('model.php');

class question_add_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $options = getAllQuestionsCreatedByUser(false);

        $mform->addElement('select', 'question', get_string('questionchoose', 'block_overachiever'), $options);
        $mform->addElement('submit', 'submitbutton', get_string('add', 'block_overachiever'));
    }
}

class question_delete_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $options = array();
        $qs = getOAQuestionsAddedByUser();
        foreach($qs as $id=>$data){
            $options[$id]=$data;
        }

        $mform->addElement('select', 'question', get_string('questionchoose', 'block_overachiever'), $options);
        $mform->addElement('submit', 'submitbutton', get_string('delete', 'block_overachiever'));
    }
}