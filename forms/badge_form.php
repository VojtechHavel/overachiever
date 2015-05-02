<?php
/**
 * Created by Vojtěch Havel on 2015/04/26
 */

require_once("$CFG->libdir/formslib.php");
require_once('model.php');

class badge_add_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $options = getAllBadgesCreatedByUser();

        $types = array(
            '1' => get_string('badgeforpoints', 'block_overachiever'),
            '2' => get_string('badgeforstreak', 'block_overachiever')
        );


        $mform->addElement('select', 'badges', get_string('badgechoose', 'block_overachiever'), $options);
        $mform->addElement('select', 'badgetype', get_string('badgetype', 'block_overachiever'), $types);
        $mform->addElement('text', 'param', get_string('badgeparam', 'block_overachiever'));
        $mform->addRule('param', get_string('badgeinsertnum', 'block_overachiever'), 'numeric');
        $mform->setType('param', PARAM_INT);
        $mform->addElement('submit', 'submitbutton', get_string('add', 'block_overachiever'));
    }
}

class badge_delete_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $options = array();
        $badges = getOABadgesAddedByUser();
        //echo var_dump($badges);
        foreach($badges as $id=>$data){
            $description = "";
            if($data["type"]==1){
                $description = get_string('badgetype1', 'block_overachiever').$data["param"];
            }
            else if($data["type"]==2){
                $description = get_string('badgetype2', 'block_overachiever').$data["param"];
            }
            else if($data["type"]==0){
                $description = get_string('badgetype0', 'block_overachiever');
            }
            else{
                $description = get_string('badgetypeelse', 'block_overachiever').$data["type"].get_string('badgeparam', 'block_overachiever').$data["param"];
            }
            $options[$id]=$data["name"].$description;
        }


        $mform->addElement('select', 'badges', get_string('badgechoose', 'block_overachiever'), $options);
        $mform->addElement('submit', 'submitbutton', get_string('delete', 'block_overachiever'));
    }
}