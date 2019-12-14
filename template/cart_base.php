<section>
<?php
    if(isset($_SESSION["cartError"])):
?>
    <p>Alcuni elementi non sono potuti essere aggiunti al carrello. Ci scusiamo per l'inconveniente.</p>
<?php
        unset($_SESSION["cartError"]);
    endif;
    if (isset($_SESSION["paymentError"])):
?>
    <p>Si è verificato un errore durante il pagamento. Si prega di riprovare.</p>
<?php
        unset($_SESSION["paymentError"]);
    endif;
    if (isset($templateParams["cartBody"])) {
        require $templateParams["cartBody"];
    }
?>
</section>