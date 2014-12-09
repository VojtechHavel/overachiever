<?php ////$Id: mod_form.php,v 1.2.2.3 2009/03/19 12:23:11 mudrd8mz Exp $
//
///**
// * This file defines the main quizit configuration form
// * It uses the standard core Moodle (>1.8) formslib. For
// * more info about them, please visit:
// *
// * http://docs.moodle.org/en/Development:lib/formslib.php
// *
// * The form must provide support for, at least these fields:
// *   - name: text element of 64cc max
// *
// * Also, it's usual to use these fields:
// *   - intro: one htmlarea element to describe the activity
// *            (will be showed in the list of activities of
// *             quizit type (index.php) and in the header
// *             of the quizit main page (view.php).
// *   - introformat: The format used to write the contents
// *             of the intro field. It automatically defaults
// *             to HTML when the htmleditor is used and can be
// *             manually selected if the htmleditor is not used
// *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
// *             See lib/weblib.php Constants and the format_text()
// *             function for more info
// */
//
//require_once($CFG->dirroot.'/course/moodleform_mod.php');
//
//class mod_quizit_mod_form extends moodleform_mod {
//
//    function definition() {
//
//        global $COURSE;
//        $mform =& $this->_form;
//
////-------------------------------------------------------------------------------
//    /// Adding the "general" fieldset, where all the common settings are showed
//        $mform->addElement('header', 'general', get_string('general', 'form'));
//
//    /// Adding the standard "name" field
//        $mform->addElement('text', 'name', get_string('quizitname', 'quizit'), array('size'=>'64'));
//        if (!empty($CFG->formatstringstriptags)) {
//            $mform->setType('name', PARAM_TEXT);
//        } else {
//            $mform->setType('name', PARAM_CLEAN);
//        }
//        $mform->addRule('name', null, 'required', null, 'client');
//        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
//
//    /// Adding the required "intro" field to hold the description of the instance
//        $mform->addElement('htmleditor', 'intro', get_string('quizitintro', 'quizit'));
//        $mform->setType('intro', PARAM_RAW);
//        $mform->addRule('intro', get_string('required'), 'required', null, 'client');
//        $mform->setHelpButton('intro', array('writing', 'richtext'), false, 'editorhelpbutton');
//
//    /// Adding "introformat" field
//        $mform->addElement('format', 'introformat', get_string('format'));
//
////-------------------------------------------------------------------------------
//    /// Adding the rest of quizit settings, spreeading all them into this fieldset
//    /// or adding more fieldsets ('header' elements) if needed for better logic
//        $mform->addElement('static', 'label1', 'quizitsetting1', 'Your quizit fields go here. Replace me!');
//
//        $mform->addElement('header', 'quizitfieldset', get_string('quizitfieldset', 'quizit'));
//        $mform->addElement('static', 'label2', 'quizitsetting2', 'Your quizit fields go here. Replace me!');
//
////-------------------------------------------------------------------------------
//        // add standard elements, common to all modules
//        $this->standard_coursemodule_elements();
////-------------------------------------------------------------------------------
//        // add standard buttons, common to all modules
//        $this->add_action_buttons();
//
//    }
//}
//
//


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
* Module instance settings form
*/
class mod_quizit_mod_form extends moodleform_mod {

/**
* Defines forms elements
*/
public function definition() {

$mform = $this->_form;

//-------------------------------------------------------------------------------
// Adding the "general" fieldset, where all the common settings are showed
$mform->addElement('header', 'general', get_string('general', 'form'));

// Adding the standard "name" field
$mform->addElement('text', 'name', get_string('quizitname', 'quizit'), array('size'=>'64'));
if (!empty($CFG->formatstringstriptags)) {
$mform->setType('name', PARAM_TEXT);
} else {
$mform->setType('name', PARAM_CLEAN);
}
$mform->addRule('name', null, 'required', null, 'client');
$mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
$mform->addHelpButton('name', 'quizitname', 'quizit');

// Adding the standard "intro" and "introformat" fields
$this->add_intro_editor();

//-------------------------------------------------------------------------------
// Adding the rest of quizgame settings, spreeading all them into this fieldset
/* or adding more fieldsets ('header' elements) if needed for better logic
$mform->addElement('static', 'label1', 'quizgamesetting1', 'Your quizgame fields go here. Replace me!');

$mform->addElement('header', 'quizgamefieldset', get_string('quizgamefieldset', 'quizgame'));
$mform->addElement('static', 'label2', 'quizgamesetting2', 'Your quizgame fields go here. Replace me!');
*/
//-------------------------------------------------------------------------------
// add standard elements, common to all modules
$this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
// add standard buttons, common to all modules
$this->add_action_buttons();
}
}


?>