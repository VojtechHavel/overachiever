<?php
/**
 * Created by Vojtěch Havel on 2014/12/15
 */
require('../../config.php');
require_once('model.php');
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT, $USER;

//require logged in regular user
if (!$USER->id || isguestuser()){
    redirect('../../');
}

$finalPage = showWithLayout(overachiever_showmenu($USER->id,$DB,$PAGE,$USER),'menu.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;


function overachiever_showmenu($userId,$DB,$PAGE,$USER) {
    //based on the users capabilities in system, we decide what capabilities he has in the game
    //add css class for feedback depending on the width of the menu (wider for teachers)
    $editing=canAdd();
    $badgeable = has_capability('moodle/badges:createbadge', context_system::instance(),$USER);

    $badges = "";
    $questions = "";
    $feedbackwidth = "menufbshort";

    if($editing){
        $feedbackwidth = "menufbwide";
        $questions ='<a href="questions.php"><div class="oa menu"><div class="purple center">'.get_string('menuquestions', 'block_overachiever').'</div></div></a>';
    };

    if($badgeable){
        $feedbackwidth =  "menufbwide";
        $badges = '<a href="badges.php"><div class="oa menu"><div class="purple center">'.get_string('menubadges', 'block_overachiever').'</div></div></a>';
    }

    //html of the menu
    $display = '
        <div id="menuwrap">
            <div>'.$questions.'
                <a href="streak.php"><div class="oa menu"><div class="center">'.get_string('collectpoints', 'block_overachiever').'</div></div></a>
                <div class="oa menu small">
                    <div id="points" class="orange oatable">
                        <div class="center">'.getUsersPoints($userId,$DB).'</div>
                    </div>
                <a href="help.php">
                    <div id="oahelp" class="oatable">
                        <div class="center">'.get_string('help', 'block_overachiever').'</div>
                    </div>
                </a>
            </div>
                <div class="newline"></div>'.$badges.'
                <a href="ladder.php"><div class="oa menu"><div class="center">'.get_string('ladder', 'block_overachiever').'</div></div></a>
                <a href="profile.php"><div class="oa menu"><div class="center">'.get_string('profile', 'block_overachiever').'</div></div></a>
                </div>
                <div class="newline"></div>
                <a href="feedback.php"><div class="oa menu" id="'.$feedbackwidth.'">
                    <div class="green center" id="menufeedbackin">'.get_string('sendfeedback', 'block_overachiever').get_string('here', 'block_overachiever').'
                </div>
            </div></a>
        </div>';
    return $display;
}
