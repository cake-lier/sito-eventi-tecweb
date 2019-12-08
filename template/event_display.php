<header>
    <h1><?php echo $templateParams["event"]["name"]; ?></h1>
    <p>Luogo: <?php echo $templateParams["event"]["place"]; ?></p>
    <p>Data e ora: <?php 
                        $date = new \DateTime($templateParams["event"]["dateTime"]);
                        $formatter = new \IntlDateFormatter("it_IT", null, null);
                        $formatter->setPattern("d MMMM yyyy");
                        echo $formatter->format($date) . " ore " . $date->format("H:i");
                   ?>
    </p>
    <p>Organizzato da: <?php echo $templateParams["event"]["organizationName"]; ?></p>
</header>
<section id="purchaseSection">
    <p>Posti ancora disponibili: <?php echo $templateParams["event"]["freeSeats"]; ?> su <?php echo $templateParams["event"]["totalSeats"]; ?></p>
    <p>
        <?php foreach($templateParams["event"]["categories"] as $category): ?>
        <span>#<?php echo $category; ?></span>
        <?php endforeach; ?>
    </p>
    <p><?php echo $templateParams["event"]["description"]; ?></p>
</section>
<footer>
    <button id="purchaseButton"><img src="<?php echo IMG_DIR ?>cart.png" alt="Vai all'acquisto"/></button>
</footer>
