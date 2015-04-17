<?php
/**
 * Created by Vojtěch Havel on 2014/12/15
 */

require('../../config.php');
require_once('model.php');
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT, $USER;
$finalPage = showWithLayout(overachiever_showmenu($USER->id,$DB),'menu.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;


function overachiever_showmenu($userId,$DB) {
    $display = '

<div>
'
//<a href="random.php">
//<div class="oa menu"><div class="center"><div>Náhodně</div></div></div>
//    </a>
  .'

<a href="survival.php"><div class="oa menu"><div class="center">'.get_string('survival', 'block_overachiever').'</div></div></a>

<div class="oa menu small">
   <div id="points" class="orange table">
        <div class="center">'.getUsersPoints($userId,$DB).'</div>
   </div>


<a href="help.php">

        <div id="oahelp" class="table">
            <div class="center">'.get_string('help', 'block_overachiever').'</div>
        </div>
</a>
</div>


<div class="newline"></div>
'
//<a href="obchod"><div class="oa menu"><div class="center">Obchod</div></div></a>
.'
<a href="ladder.php"><div class="oa menu"><div class="center">'.get_string('ladder', 'block_overachiever').'</div></div></a>
<a href="profile.php"><div class="oa menu"><div class="center">'.get_string('profile', 'block_overachiever').'</div></div></a>

</div>'

        .'<div class="newline"></div>'
  .'<a href="feedback.php"><div class="oa menu" id="menufeedback">
            <div class="green center" id="menufeedbackin">'.get_string('sendfeedback', 'block_overachiever').
                ''.get_string('here', 'block_overachiever').'
            </div>
    </div></a>';


    return $display;
}
