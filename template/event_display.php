<header>
    <h1><?php echo $templateParams["event"]["name"]; ?></h1>
    <p>Luogo: <?php echo $templateParams["event"]["place"]; ?></p>
    <p>Data e ora: <?php echo $templateParams["event"]["dateTime"]; ?></p>
    <p>Organizzato da: <?php echo $templateParams["event"]["organizationName"]; ?></p>
</header>
<section id="purchaseSection">
    <p>
    Posti ancora disponibili: <?php echo $templateParams["event"]["freeSeats"]; ?> su
    <?php echo $templateParams["event"]["totalSeats"]; ?>
    </p>
    <p>
        <?php foreach($templateParams["event"]["categories"] as $category): ?>
        <span>#<?php echo $category; ?></span>
        <?php endforeach; ?>
    </p>
    <p><?php echo $templateParams["event"]["description"]; ?></p>
</section>
<?php 
    if ($templateParams["isLoggedUserCustomer"]):
?>
    <footer>
        <button type="button" id="purchaseButton"><img src="<?php echo IMG_DIR ?>cart.png" alt="Vai all'acquisto"/></button>
    </footer>
<?php
    endif;
?>
