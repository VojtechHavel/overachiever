<?php
/**
 * Created by Vojtěch Havel on 2014/12/15
 */

require_once('../../config.php');
require_once('model.php');
require_once('utility.php');
global $DB, $COURSE, $PAGE, $OUTPUT, $USER;
$finalPage = showWithLayout(overachiever_showmenu($USER->id,$DB),'menu.php',$DB, $COURSE, $PAGE, $OUTPUT);
echo $finalPage;


function overachiever_showmenu($userId,$DB) {
    $display = '
<div>
<a href="random.php">
<div class="oa menu"><div class="center"><div>Náhodně</div></div></div>
    </a>
<a href="survival.php"><div class="oa menu"><div class="center">Náhlá smrt</div></div></a>

<div class="oa menu small">
   <div id="body" class="table">
        <div class="center">'.getUsersPoints($userId,$DB).'</div>
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
<a href="profil.php"><div class="oa menu"><div class="center">Profil</div></div></a>

</div>';


    return $display;
}
