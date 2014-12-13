<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */

defined('MOODLE_INTERNAL') || die();


class block_overachiever extends block_base {

    public function init() {
        $this->title = get_string('overachiever', 'block_overachiever');
    }

    public function specialization() {
        if (empty($this->config->title)) {
            $this->config->title = get_string('overachiever', 'block_overachiever');
        }
        $this->title = $this->config->title;
    }

    public function get_content() {
        global $COURSE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;

        //hide from guests
        if (!$USER->id || isguestuser()) {
            return $this->content;
        }

        if (empty($this->config->description)) {
            $this->config->description = get_string('defaultdescription', 'block_overachiever');
        }
        $this->content->text = $this->config->description;

        $url = new moodle_url('/blocks/overachiever/view.php', array('courseid' => $COURSE->id));
        $this->content->footer = html_writer::link($url, get_string('blocklink', 'block_overachiever'));
        return $this->content;
    }
}