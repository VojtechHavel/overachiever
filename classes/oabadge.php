<?php
/**
 * Created by Vojtěch Havel on 2015/03/06
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ .'/../model.php');

abstract class BadgeFactory
{
    static function create($type, $args = NULL)
    {
        if ($type == 1) {
            return new PointsBadge($args);
        }
        else if($type == 2){
            return new StreakBadge($args);
        }
    }
}


interface iOaBadge
{
    public function conditionsMet();

    public function popupContent();
}

class PointsBadge implements iOaBadge
{
    public $points;
    public $badgeid;

    public function __construct($points = 0)
    {
        $this->points = $points;
    }

    public function conditionsMet()
    {
        global $DB;
        if($this->points<=getCurrentUserPoints())
        return true;
    }

    public function popupContent()
    {

        $content = get_string('survival', 'block_overachiever');

        return $content;
    }


}

class StreakBadge implements iOaBadge
{
    public $streak;
    public $badgeid;

    public function __construct($streak = 0)
    {
        $this->streak = $streak;
    }

    public function conditionsMet()
    {
        global $DB;
        if($this->streak<=getRecordStreak())
            return true;
    }

    public function popupContent()
    {

        $content = 'grats';

        return $content;
    }


}