<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/overachiever/lib/BadgeUtils.php');


class block_overachiever extends block_base {

    public function init() {
        $this->title = get_string('overachiever', 'block_overachiever');
    }

    public function specialization() {
        if (empty($this->config->title)) {
            if(!$this->config){$this->config =  new stdClass();}

            $this->config->title = get_string('overachiever', 'block_overachiever');
        }

    }

    public function get_content() {
        global $COURSE, $USER, $DB;
        require_once('model.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;

        //hide from guests
        if (!$USER->id || isguestuser()) {
            return $this->content;
        }

        if (empty($this->config->description)) {
            if(!$this->config){$this->config =  new stdClass();}
            $this->config->description = get_string('defaultdescription', 'block_overachiever');
        }

        //show users points in block panel
        $points = getUsersPoints($USER->id, $DB);
        $this->content->text = '<div style="background-color:#FF9900; padding:3px;display: inline-block;  border-radius:10px">'.$points.' '.get_string("xpoints", "block_overachiever").'</div><br>';
        $this->content->text .= $this->config->description;

        //check if user met conditions for new badge
        //if he has ward badge and display popup
        if($newBadgeText = badgeUtils::getBadgePopup($USER->id)) {
            $this->content->text .= $newBadgeText;
        }

        $url = new moodle_url('/blocks/overachiever/menu.php', array('courseid' => $COURSE->id));
        $this->content->footer = html_writer::link($url, get_string('blocklink', 'block_overachiever'));
        return $this->content;
    }
}