<?php
    $event = $templateParams["mostPopularEvent"];
    require "template/event_tab.php";
?>
<a class="button_no_image" id="discover_link" href="search.php?keyword=">Scopri di più</a>
<?php
    $event = $templateParams["mostRecentEvent"];
    require "template/event_tab.php";
?>