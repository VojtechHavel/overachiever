<?php
/**
 * Created by Vojtěch Havel on 2015/01/08
 */
require_once(dirname(__FILE__) . '/../../config.php');
global $DB, $USER;
require_once($CFG->libdir . '/badgeslib.php');

echo 'badges';
$name = 'Star';
$badge = $DB->get_record('badge', array('name' => $name));
$newbadge = new badge($badge->id);
echo $newbadge->type;
$newbadge->issue(4);