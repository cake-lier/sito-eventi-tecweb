<section>
<?php
    $eventId = $dbh->getEventsManager()->getMostPopularEvent();
    if ($eventId !== false) {
        $event = $dbh->getEventsManager()->getEventInfo($eventId);
        if ($event !== false) {
            require "template/event_tab.php";
        }
    }
?>
<a href="search.php?keyword=">Scopri di più</a>
<?php
    $eventId = $dbh->getEventsManager()->getMostRecentEvent();
    if ($eventId !== false) {
        $event = $dbh->getEventsManager()->getEventInfo($eventId);
        if ($event !== false) {
            require "template/event_tab.php";
        }
    }
?>
</section>