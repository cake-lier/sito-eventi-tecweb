<section id="cart_payment_section">
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
        <section class="event_tickets">
            <header>
                <h1><?php echo $ticket["eventName"]; ?></h1>
                <p><?php echo $ticket["eventPlace"] . ", " . $ticket["dateTime"]; ?></p>
            </header>
            <section>
                <p><?php echo $ticket["category"]; ?></p>
                    <a class="button" href="#" id="dec_button_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">
                        <img src="img/less.png" alt="Togli un biglietto"/>
                    </a>
                    <p><?php echo $ticket["amount"] . " bigliett" . ($ticket["amount"] > 1 ? "i" : "o"); ?></p>
                    <a class="button" href="#" id="inc_button_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">
                        <img src="img/more.png" alt="Aggiungi un biglietto"/>
                    </a>
                <p><?php echo $ticket["price"] . "€/cad." ?></p>
                <a class="button_no_image" href="#" id="remove_button_<?php echo $ticket["seatId"] . "_" . $ticket["eventId"]; ?>">
                    Rimuovi
                </a>
            </section>
        </section>
    <?php
        endforeach;
    ?>
</section>