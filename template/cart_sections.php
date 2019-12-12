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
                    <button>-</button>
                    <p><?php echo $ticket["amount"] . " bigliett" . ($ticket["amount"] > 1 ? "i" : "o"); ?></p>
                    <button>+</button>
                    <p><?php echo $ticket["price"] . "€/cad." ?></p>
                </section>
                <button id="removeButton">Rimuovi</button>
            </section>
        </section>
    <?php
        endforeach;
    ?>
</section>