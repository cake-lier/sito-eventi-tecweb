<section class="event">
    <header>
        <h3><a href="event.php?id=<?php echo $event["id"]; ?>"><?php echo $event["name"]; ?></a></h3>
        <p><a href="search.php?place=<?php echo urlencode($event["place"]); ?>&posti=free"><?php echo $event["place"]; ?></a>, <?php echo $event["dateTime"]; ?></p>
        <p>Organizzato da <a href="search.php?promoter=<?php echo urlencode($event["organizationName"]); ?>&posti=free"><?php echo $event["organizationName"]; ?></a></p> 
    </header>
    <section class="info_section">
        <p>Posti ancora disponibili: <?php echo $event["freeSeats"]; ?> su <?php echo $event["totalSeats"]; ?></p>
        <p>
        <?php
            foreach ($event["categories"] as $category):
        ?>
        <a href="search.php?tags=%23<?php echo $category ?>&posti=free">#<?php echo $category; ?></a>
        <?php
            endforeach;
        ?>
        </p>
        <?php 
            if (isset($templateParams["isUser"]) && $templateParams["isUser"] && isset($event["tickets"])) {
                echo "<p>Posti acquistati:</p><ul id=\"purchased_tickets_list\">";
                foreach ($event["tickets"] as $ticket) {
                    echo "<li>".$ticket["seatQuantity"]." ".$ticket["seatName"]."</li>";
                }
                echo "</ul>";
            }
        ?>
    </section>
    <footer>
    <?php if ($event["isLoggedUserEventOwner"]): ?>
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