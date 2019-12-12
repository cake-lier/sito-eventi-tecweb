<section>
<?php
    $event = $dbh->getEventsManager()->getEventInfo($dbh->getEventsManager()->getMostPopularEvent());
    require "template/event_tab.php";
?>
<?php
    $event = $dbh->getEventsManager()->getEventInfo($dbh->getEventsManager()->getMostRecentEvent());
    require "template/event_tab.php";
?>
<a href='./search.php?keyword=""'>Scopri di più</a>
</section>