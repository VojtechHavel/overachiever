<?php
/**
 * Created by Vojtěch Havel on 2014/12/12
 */
defined('MOODLE_INTERNAL') || die();
require_once("overachiever_form.php");

/**
 * Class block_overachiever_edit_form
 * sets default parameters for instance of a block
 */
class block_overachiever_edit_form extends overachiever_form {
    public $defaultregion;
    public $defaultweight;
    public $visible;
    public $region;
    public $weight;

    function set_instance_data($defaults){
        $this->defaultregion = $defaults['defaultregion'];
        $this->defaultweight = $defaults['$defaultweight'];
        $this->visible = $defaults['visible'];
        $this->region = $defaults['region'];
        $this->weight = $defaults['weight'];
    }
}