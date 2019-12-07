<?php
    foreach ($templateParams["events"] as $event):
?>
<section>
    <header>
        <h1><a href="event.php?id=<?php echo $event["id"]; ?>"><?php echo $event["name"]; ?></a></h1>
        <p><?php echo $event["place"]; ?>, <?php echo $event["dateTime"]; ?></p>
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
