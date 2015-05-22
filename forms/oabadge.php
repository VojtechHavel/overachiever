<?php
/**
 * Created by Vojtěch Havel on 2015/03/06
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ .'/../model.php');

/**
 * Class BadgeFactory
 * factory method for creating 3 types of badges
 */
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
        else if($type == 0){
            return new FeedbackBadge();
        }

    }
}

/**
 * Interface iOaBadge
 * interface for badge types
 */
interface iOaBadge
{
    public function conditionsMet();

    public function popupContent();
}

/**
 * Class PointsBadge
 * badge warded for earning x-amount of points
 */
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

        $content = get_string('congrats', 'block_overachiever');

        return $content;
    }


}

/**
 * Class StreakBadge
 * badge warded for x-long streak
 */
class StreakBadge implements iOaBadge
{
    public $streak;
    public $badgeid;

    public function __construct($streak = 0)
    {
        $this->streak = $streak;
    }

    //check whether user reached conditions to receive this badge
    public function conditionsMet()
    {
        global $DB;
        if($this->streak<=getRecordStreak())
            return true;
    }

    //set content of popup that shows when badge is warded to the user
    public function popupContent()
    {

        $content = get_string('congrats', 'block_overachiever');

        return $content;
    }


}

/**
 * Class FeedbackBadge
 * badge awarded for feedback
 */
class FeedbackBadge implements iOaBadge
{
    public $badgeid;

    public function __construct()
    {
    }

    public function conditionsMet()
    {
        return false;
    }

    public function popupContent()
    {

        $content = get_string('congrats', 'block_overachiever');

        return $content;
    }


}