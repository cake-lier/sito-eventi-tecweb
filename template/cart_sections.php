<?php
    if (isset($_GET["error"])):
?>
    <p>Si è verificato un errore durante il pagamento. Si prega di riprovare.</p>
<?php
    endif;
?>
<section>
    <?php
        if (isset($templateParams["cartPaymentSection"])) {
            require $templateParams["cartPaymentSection"];
        }
    ?>
</section>
<section>
    <?php
        foreach ($templateParams["tickets"] as $ticket):
    ?>
        <section>
            <header>
                <h1><?php echo $ticket["eventName"]; ?></h1>
                <p><?php echo $ticket["eventPlace"] . ", " . $ticket["dateTime"]; ?></p>
            </header>
            <section>
                <section>
                    <p><?php echo $ticket["category"]; ?></p>
                    <button type="button" id="decButton_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">-</button>
                    <p><?php echo $ticket["amount"] . " bigliett" . ($ticket["amount"] > 1 ? "i" : "o"); ?></p>
                    <button type="button" id="incButton_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">+</button>
                    <p><?php echo $ticket["price"] . "€/cad." ?></p>
                </section>
                <button type="button" id="removeButton_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">Rimuovi</button>
            </section>
        </section>
    <?php
        endforeach;
    ?>
</section>