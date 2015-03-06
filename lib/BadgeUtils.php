<?php

/**
 * Created by Vojtěch Havel on 2015/03/06
 */

namespace OA\Utils;

class BadgeUtils
{
    public static function getBadgePopup()
    {
        $text = '<div id="my_popup">

    ...popup content...

    <!-- Add an optional button to close the popup -->
    <button class="my_popup_close">Close</button>

  </div>
  <!-- Include jQuery -->
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>

  <!-- Include jQuery Popup Overlay -->
  <script src="http://vast-engineering.github.io/jquery-popup-overlay/jquery.popupoverlay.js"></script>

  <script>
    $(document).ready(function() {

      // Initialize the plugin
      $("#my_popup").popup();
      $("#my_popup").popup("show");

    });
</script>  ';
        return $text;
}
}