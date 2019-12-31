<section class="event">
    <header>
        <h1><?php echo $templateParams["event"]["name"]; ?></h1>
        <p>Luogo: <?php echo $templateParams["event"]["place"]; ?></p>
        <p>Data e ora: <?php echo $templateParams["event"]["dateTime"]; ?></p>
        <p>Organizzato da: <a href="http://localhost/search.php?keyword=&tags=&place=&promoter=<?php echo $event["organizationName"]; ?>&date=&posti=free&search=Cerca"><?php echo $event["organizationName"]; ?></a></p> 
    </header>
    <section id="purchase_section" class="info_section">
        <p>
        Posti ancora disponibili: <?php echo $templateParams["event"]["freeSeats"]; ?> su
        <?php echo $templateParams["event"]["totalSeats"]; ?>
        </p>
        <p>
            <?php foreach($templateParams["event"]["categories"] as $category): ?>
                <a href="search.php?keyword=&tags=%23<?php echo $category ?>&place=&date=&posti=free&search=Cerca">#<?php echo $category; ?></a>
            <?php endforeach; ?>
        </p>
        <p><?php echo $templateParams["event"]["description"]; ?></p>
    </section>
    <?php 
        if ($templateParams["isLoggedUserCustomer"]):
    ?>
        <footer>
            <a class="button" id="purchase_button"><img src="<?php echo IMG_DIR ?>cart.png" alt="Vai all'acquisto"/></a>
        </footer>
    <?php
        endif;
    ?>
</section>
