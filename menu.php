<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module quizgame
 *
 * All the quizgame specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_quizgame
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

/**
 * Initialises the game and returns its HTML code
 *
 * @param context $context The context
 * @return string The HTML code of the game
 */
function overachiever_showmenu() {
//    global $PAGE, $DB;
//
////    $PAGE->requires->strings_for_js(array(
////            'score',
////            'emptyquiz',
////            'endofgame',
////            'spacetostart'
////        ), 'mod_quizgame');
////    $PAGE->requires->js('/mod/quizgame/quizgame.js');
//
//    $categories = $DB->get_records('question_categories', array('contextid' => $context->get_parent_context()->__get('id')));
//    $category_ids = [];
//    foreach ($categories as $category) {
//        $category_ids[] = $category->id;
//    }
//
//    $questions = question_load_questions(null);
//


    $display = '
<div>
<a href="nahodne">
<div class="oa menu"><div class="center"><div>Náhodně</div></div></div>
    </a>
<a href="NahlaSmrt"><div class="oa menu"><div class="center">Náhlá smrt</div></div></a>

<div class="oa menu small">
   <div id="body" class="table">
        <div class="center">500 bodů</div>
   </div>


<a href="help.php">

        <div id="oahelp" class="table">
            <div class="center">Nápověda</div>
        </div>
</a>
</div>


<div class="newline"></div>
<a href="obchod"><div class="oa menu"><div class="center">Obchod</div></div></a>
<a href="zebricek"><div class="oa menu"><div class="center">Žebříček</div></div></a>
<a href="profil"><div class="oa menu"><div class="center">Profil</div></div></a>

</div>';


    return $display;
}

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function quizgame_do_something_useful(array $things) {
//    return new stdClass();
//}
