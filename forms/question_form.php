<?php
/**
 * Created by Vojtěch Havel on 2015/04/27
 */

require_once("$CFG->libdir/formslib.php");
require_once('model.php');

/**
 * Class question_add_form
 * form for adding questions in question administration ("Manage questions")
 */
class question_add_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        //get questions that user can add
        $options = getAllQuestionsCreatedByUser(false);

        //create form
        $mform->addElement('select', 'question', get_string('questionchoose', 'block_overachiever'), $options);
        $mform->addElement('submit', 'submitbutton', get_string('add', 'block_overachiever'));
    }
}

/**
 * Class question_delete_form
 * form for deleting questions in question administration ("Manage questions")
 */
class question_delete_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $options = array();

        //get questions that user can delete
        $qs = getOAQuestionsAddedByUser();
        foreach($qs as $id=>$data){
            $options[$id]=$data;
        }

        //create form
        $mform->addElement('select', 'question', get_string('questionchoose', 'block_overachiever'), $options);
        $mform->addElement('submit', 'submitbutton', get_string('delete', 'block_overachiever'));
    }
}