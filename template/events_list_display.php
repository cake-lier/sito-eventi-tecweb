<section>
    <?php
        foreach ($templateParams["events"] as $event):
    ?>
    <section>
        <header>
            <h1><a href="event.php?id=<?php echo $event["id"]; ?>"><?php echo $event["name"]; ?></a></h1>
            <p><?php echo $event["place"]; ?>, <?php 
                                                        $date = new \DateTime($event["dateTime"]);
                                                        $formatter = new \IntlDateFormatter("it_IT", null, null);
                                                        $formatter->setPattern("d MMMM yyyy");
                                                        echo $formatter->format($date) . " ore " . $date->format("H:i");
                                                ?></p>
            <p>Organizzato da <?php echo $event["organizationName"]; ?></p> 
        </header>
        <section>
            <p>Posti ancora disponibili: <?php echo $event["freeSeats"]; ?> su <?php echo $event["totalSeats"]; ?></p>
        </section>
        <footer>
            <?php
                foreach ($event["categories"] as $category):
            ?>
            <span>#<?php echo $category; ?></span>
            <?php
                endforeach;
            ?>
        </footer>
    </section>
    <?php
        endforeach;
    ?>
</section>
<footer>
<button id="previousPage"><img src="back.png" alt="Pagina precedente"/></button>
<button id="nextPage"><img src="next.png" alt="Pagina successiva"/></button>
</footer>
