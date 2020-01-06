<?php
$text = "";
$title = "";
if (isset($_GET["type"])) {
    switch ($_GET["type"]) {
        case "chi_siamo":
            $text = "SeatHeat é un sito per la vendita di biglietti di eventi."
                    ."Ringraziamo <a href=\"https://www.streamlineicons.com/\">Streamline</a> per le icone, "
                    ."e Antoine Delanoix per l'immagine nella home.";
            $title = "Chi siamo";
        break;
        case "contatti":
            $text = "Per contattarci, manda una mail a seatheat@seatheat.it, "
                    ."oppure invia un messaggio agli admin tramite l'apposita "
                    ."sezione!";
            $title = "Contatti";
        break;
        case "termini":
            $text = "Termini di servizio";
            $title = "Termini di servizio";
        break;
        case "privacy":
            $text = "Il sito usa solo cookie tecnici al fine di fornire i nostri servizi nel miglior modo possibile. "
                    ."I dati personali che trattiamo sono unicamente quelli richiesti in fase di registrazione, é "
                    ."possibile trovarne un riepilogo nella propria area personale. I dati sono mantenuti nel nostro "
                    ."database, non necessarimente in territorio europeo, fino alla chiusura dell'account, effettuabile "
                    ."dall'apposita sezione nell'area personale. ";
            $title = "Informativa privacy";
        break;
        default:
            $text = "Non possiamo fornirti questo tipo di informazioni!";
            $title = "404";
    }
}
?>
<section>
    <h2><?php echo $title; ?></h2>
    <p><?php echo $text; ?></p>
</section>
