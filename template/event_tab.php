<section class="event">
    <header>
        <h3><a href="event.php?id=<?php echo $event["id"]; ?>"><?php echo $event["name"]; ?></a></h3>
        <p><?php echo $event["place"]; ?>, <?php echo $event["dateTime"]; ?></p>
        <p>Organizzato da <a href="http://localhost/search.php?keyword=&tags=&place=&promoter=<?php echo $event["organizationName"]; ?>&date=&posti=free&search=Cerca"><?php echo $event["organizationName"]; ?></a></p> 
    </header>
    <section class="info_section">
        <p>Posti ancora disponibili: <?php echo $event["freeSeats"]; ?> su <?php echo $event["totalSeats"]; ?></p>
        <p>
        <?php
            foreach ($event["categories"] as $category):
        ?>
        <a href="search.php?keyword=&tags=%23<?php echo $category ?>&place=&date=&posti=free&search=Cerca">#<?php echo $category; ?></a>
        <?php
            endforeach;
        ?>
        </p>
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