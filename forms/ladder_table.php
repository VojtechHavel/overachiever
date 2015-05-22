<?php

/**
 * Created by Vojtěch Havel on 2014/12/25
 */

defined('MOODLE_INTERNAL') || die();
require_once('model.php');

/**
 * Class ladder_table
 * defines table that is displayed as ladder
 */
class ladder_table extends table_sql{

    private $rank = 0;

    function __construct($id, $DB) {
        parent::__construct($id,$DB);

        //columns of table
        $columns = array('rank','firstname','userpic', 'points', 'streak');
        $this->define_columns($columns);

        //add columns that are not sortable (userpic is default)
        $this->column_nosort[] = "rank";

        //set default column for sorting
        $this->sort_default_column = "points";
        $this->sort_default_order  = SORT_DESC;


        //headers of table
        $headers = array(get_string('rank', 'block_overachiever'),
            get_string('username', 'block_overachiever'),
            get_string('userpic', 'block_overachiever'),
            get_string('points', 'block_overachiever'),
            get_string('streak', 'block_overachiever'));

        $this->define_headers($headers);

        $this->sql = new stdClass();
        $this->sql->fields = 'o.*, ' . user_picture::fields('u').', s.streak, o.points AS rank';
        $this->sql->from = '{block_oa_users} o
                            LEFT JOIN {user} u ON o.user = u.id
                            LEFT JOIN {block_oa_streak} s ON o.user = s.user
                            ';
        $this->sql->where = 1;
        $this->sql->params = array('points' => SORT_ASC);

        //disable columns to be collapsible
        $this->collapsible(false);
    }


    //format rows
    function  build_table(){
        global $USER;
        if ($this->rawdata) {
            foreach ($this->rawdata as $row) {

                //highlight current user - user that is vieving this ladder
                $highlight = ($USER->id == $row->user) ? 'highlight' : '';
                $formattedrow = $this->format_row($row);
                $this->add_data_keyed($formattedrow, $highlight);
            }
            }
    }

    //format column with user's picture
    protected function col_userpic($row) {
        global $OUTPUT;
        return $OUTPUT->user_picture($row);
    }

    //format column rank
    protected function col_rank($row) {
        $this->rank = $this->rank +1;
        return $this->rank+$this->currpage*$this->pagesize;
    }

    //format column firstname
    function col_firstname($values) {
        // while downloading show text and not html
        if ($this->is_downloading()) {
            return $values->firstname;
        } else {
            return '<a href="'.new moodle_url("/user/profile.php?id=".$values->id).'">'.$values->firstname.' ' .$values->lastname.'</a>';
        }
    }

    //format all other columns
    function other_cols($colname, $value) {
    }
}

