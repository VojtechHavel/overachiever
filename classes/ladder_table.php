<?php

/**
 * Created by Vojtěch Havel on 2014/12/25
 */

defined('MOODLE_INTERNAL') || die();
require_once('model.php');

class ladder_table extends table_sql{

    private $rank = 0;
    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid, $DB) {
        parent::__construct($uniqueid,$DB);
        // Define the list of columns to show.
        $columns = array('rank','firstname','userpic', 'points', 'streak');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.


        $headers = array('Rank', 'Name','Profile picture','Points', 'Streak');
       // $this->sql = getUsers($DB);
        //var_dump($this->sql);

        $this->sql = new stdClass();
        $this->sql->fields = 'o.*, ' . user_picture::fields('u').', s.streak, o.points AS rank';
        $this->sql->from = '{block_oa_users} o
                            LEFT JOIN {user} u ON o.user = u.id
                            LEFT JOIN {block_oa_streak} s ON o.user = s.user
                            ';
        $this->sql->where = 1;
//        $this->sql->where = 'courseid = :courseid';
//        $this->sql->params = array('courseid' => $courseid);
        $this->sql->params = array('points' => SORT_ASC);
//        $result = $DB->get_records_sql('SELECT p.points,u.firstname, u.lastname, u.id FROM {block_oa_users} AS p INNER JOIN {user} AS u ON p.user=u.id
//                                    ORDER BY p.points DESC');

   //     $this->no_sorting('userpic');
        $this->define_headers($headers);
        $this->collapsible(false);

//
//        foreach ($this->rawdata as $row) {
//            $rank = $rank +1;
//            if(!$this->ranks[$row->points]){
//                $this->ranks[$row->points] = $rank;
//            }
//        }


    }


    function  build_table(){
        global $USER;
        $rank = 0;
        if ($this->rawdata) {
            foreach ($this->rawdata as $row) {

                $classes = ($USER->id == $row->user) ? 'highlight' : '';
                $formattedrow = $this->format_row($row);
                $this->add_data_keyed($formattedrow, $classes);

            }
            }

    }
//
//    public function get_sort_columns() {
//       // return array('points' => SORT_DESC);
//    }


    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */
//    function col_username($values) {
//        // If the data is being downloaded than we don't want to show HTML.
//        if ($this->is_downloading()) {
//            return $values->username;
//        } else {
//            return '<a href="/user/profile.php?id='.$values->id.'">'.$values->username.'</a>';
//        }
//    }


    protected function col_userpic($row) {
        global $OUTPUT;
        return $OUTPUT->user_picture($row);
    }

    protected function col_rank($row) {
        $this->rank = $this->rank +1;
        return $this->rank+$this->currpage*$this->pagesize;
    }

//    protected function col_qcorrect($row) {
//        global $DB;
//        if(isset($this->ranks[$row->points])){
//            return $this->ranks[$row->points];
//        }
//        else return 'N/A';
//    }

    function col_firstname($values) {
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->firstname;
        } else {
            return '<a href="'.new moodle_url("/user/profile.php?id=".$values->id).'">'.$values->firstname.' ' .$values->lastname.'</a>';
        }
    }
    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    function other_cols($colname, $value) {
    }
}

