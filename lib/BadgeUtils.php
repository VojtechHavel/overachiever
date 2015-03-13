<?php

/**
 * Created by Vojtěch Havel on 2015/03/06
 */

require_once(dirname(__FILE__) . '../../../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once(__DIR__ .'/../classes/oabadge.php');
require_once(__DIR__ .'/../model.php');

    abstract class BadgeUtils
    {

        public static function getAvailableBadges($userId)
        {
            $allBadges = getOABadges();
            $availableBadges = array();
            if($allBadges) {
                foreach ($allBadges as $badge) {
                    $notIssuedAndExists = false;
                    try{
                        $notIssuedAndExists = !self::isBadgeIssued($badge->badgeid, $userId);
                    }
                    catch(Exception $e){
                    }
                    if ($notIssuedAndExists) {
                        $availableBadges[] = $badge;
                    }
                }
            }
            return $availableBadges;
        }

        public static function getPopupContentAndAwardBadges($userId)
        {
            $content = '';
            $badges = self::getAvailableBadges($userId);
            if ($badges) {
                foreach ($badges as $badge) {
                    if ($newOaBadge = BadgeFactory::create($badge->type, $badge->param)) {
                        if ($newOaBadge->conditionsMet()) {
                            $awardedBadge = new badge($badge->badgeid);
                            $awardedBadge->issue($userId);
                            $content.= $newOaBadge ->popupContent();
                            $content.='<h2>'.$awardedBadge->name.'</h2>';
                            $context = $awardedBadge->get_context();
                            $content.=print_badge_image($awardedBadge, $context, 'large');
                            $content.='<p>'.$awardedBadge->description.'</p>';
                            $content.='<p></p>';
                        };
                    }
                }
            }

            return $content;
        }

        public static function isBadgeIssued($badgeId, $userId)
        {
            $badge = new badge($badgeId);
            return $badge->is_issued($userId);
        }

        public static function getBadgePopup($userId)
        {
            if ($popupContent = self::getPopupContentAndAwardBadges($userId)) {
                $text = '<div id="badge_popup">
' . $popupContent . '
<div>
    <button class="badge_popup_close">' . get_string("great", "block_overachiever") . '</button>
</div>
  </div>
             <style>
                .popup_content
                {
                    background-color:white;
                    padding:20px;
                    border-radius:10px;
                    }
             </style>
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://vast-engineering.github.io/jquery-popup-overlay/jquery.popupoverlay.js"></script>

  <script>
        $(document).ready(function() {
            $("#badge_popup").popup({
                opacity: 0.7,
                scrolllock: true
            });
            $("#badge_popup").popup("show");
        });
  </script>';

                return $text;
            }
        }
}