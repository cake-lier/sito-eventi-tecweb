<section>
<?php
    $event = $templateParams["mostPopularEvent"];
    require "template/event_tab.php";
?>
<a href="search.php?keyword=">Scopri di più</a>
<?php
    $event = $templateParams["mostRecentEvent"];
    require "template/event_tab.php";
?>
</section>