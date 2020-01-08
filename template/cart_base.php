<?php
    if(isset($_SESSION["cartError"])):
?>
    <section class="alert">
        <p>Alcuni elementi non sono potuti essere aggiunti al carrello. Ci scusiamo per l'inconveniente.</p>
        <a href="#"><img src="img/close.png" alt="Chiudi"/></a>
    </section>
<?php
        unset($_SESSION["cartError"]);
    endif;
    if (isset($_SESSION["paymentError"])):
?>
    <section class="alert">
        <p>Si è verificato un errore durante il pagamento. Si prega di riprovare.</p>
        <a href="#"><img src="img/close.png" alt="Chiudi"/></a>
    </section>
<?php
        unset($_SESSION["paymentError"]);
    endif;
    if (isset($templateParams["cartBody"])) {
        require $templateParams["cartBody"];
    }
?>