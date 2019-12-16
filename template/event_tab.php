<section>
    <header>
        <h1><a href="event.php?id=<?php echo $event["id"]; ?>"><?php echo $event["name"]; ?></a></h1>
        <p><?php echo $event["place"]; ?>, <?php 
                                                $date = new \DateTime($event["dateTime"]);
                                                $formatter = new \IntlDateFormatter("it_IT", null, null);
                                                $formatter->setPattern("d MMMM yyyy");
                                                echo $formatter->format($date) . " ore " . $date->format("H:i");
                                           ?>
        </p>
        <p>Organizzato da <?php echo $event["organizationName"]; ?></p> 
    </header>
    <section>
        <p>Posti ancora disponibili: <?php echo $event["freeSeats"]; ?> su <?php echo $event["totalSeats"]; ?></p>
        <p>
        <?php
            foreach ($event["categories"] as $category):
        ?>
        <span>#<?php echo $category; ?></span>
        <?php
            endforeach;
        ?>
        </p>
    </section>
    <footer>
    <?php if (isset($_SESSION["email"])
              && $dbh->getUsersManager()->isPromoter($_SESSION["email"]) 
              && $event["promoterEmail"] === $_SESSION["email"]): ?>
        <a class="button" href="modify_event_page.php?id=<?php echo $event["id"]; ?>">
            <img src="<?php echo IMG_DIR; ?>new.png" alt="modifica" />
        </a>
    <?php else: ?>
        <a class="button" href="event.php?id=<?php echo $event["id"]; ?>">
            <img src="<?php echo IMG_DIR; ?>more.png" alt="modifica" />
        </a>
    <?php endif ?>
    </footer>
</section>