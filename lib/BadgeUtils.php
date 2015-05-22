<?php

/**
 * Created by Vojtěch Havel on 2015/03/06
 */

require_once(dirname(__FILE__) . '../../../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once(__DIR__ . '/../forms/oabadge.php');
require_once(__DIR__ .'/../model.php');

/**
 * Class BadgeUtils
 * library class for working with badges
 */
    abstract class BadgeUtils
    {

        /**
         * @param $userId id of user
         * @param $type false for feedback, true for else
         * @return array of available badges
         */
        public static function getAvailableBadges($userId,$type)
        {
            if($type) {
                $allBadges = getOABadges();
            }
            else{
                $allBadges =getOAFeedbackBadges();
            }
            $availableBadges = array();
            if($allBadges) {
                foreach ($allBadges as $badge) {
                    $notIssuedAndExists = false;
                    //check if badge was already awarded and if it exists
                    try{
                        $notIssuedAndExists = !self::isBadgeIssued($badge->badgeid, $userId);
                    }
                    catch(Exception $e){
                    }
                    //else add to the array of available badges
                    if ($notIssuedAndExists) {
                        $availableBadges[] = $badge;
                    }
                }
            }
            return $availableBadges;
        }

        /**
         * @param $userId id of user
         * @param $type of badge (false - feedback, true - all else)
         * @return string -content of popup
         */
        public static function getPopupContentAndAwardBadges($userId, $type)
        {
            $content = '';
            $badges = self::getAvailableBadges($userId, $type);
            if ($badges) {
                foreach ($badges as $badge) {
                    if ($newOaBadge = BadgeFactory::create($badge->type, $badge->param)) {
                        //check condition and award badge
                        if ($newOaBadge->conditionsMet()||$type==0) {
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

        /**
         * @param $badgeId id of badge
         * @param $userId id of user
         * @return mixed true if issued
         */
        public static function isBadgeIssued($badgeId, $userId)
        {
            $badge = new badge($badgeId);
            return $badge->is_issued($userId);
        }

        /**
         * @param $userId
         * @param int $type false for feedback badges, true for all else
         * @return string
         */
        public static function getBadgePopup($userId, $type = 1)
        {
            //check if there is new badge to be awarded and shown
            if ($popupContent = self::getPopupContentAndAwardBadges($userId, $type)) {
                //set content of popup - using jquery.popupoverlay.js
                $text = '   <div id="badge_popup">'.$popupContent.'
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
                                $("#badge_popup").popup
                                ({
                                    opacity: 0.7,
                                    scrolllock: true
                                });
                            $("#badge_popup").popup("show");

                            </script>';
                return $text;
            }
        }
    }