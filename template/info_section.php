<?php
$text = "";
$title = "";
if (isset($_GET["type"])) {
    switch ($_GET["type"]) {
        case "chi_siamo":
            $text = "SeatHeat é un sito per la vendita di biglietti di eventi. Icone da <a href=\"https://www.streamlineicons.com/\">Streamline</a>.
            Immagine della home di Antoine Delanoix.";
            $title = "Chi siamo";
        break;
        case "contatti":
            $text = "Per contattarci, manda una mail a seatheat@seatheat.it";
            $title = "Contatti";
        break;
        case "termini":
            $text = "Termini di servizio";
            $title = "Termini di servizio";
        break;
        case "privacy":
            $text = "Non ci frega nulla dei vostri dati. Li teniamo sul database (non in Europa). Li terremo finché sarete nostri 
                     clienti. Gli organizzatori possono vederli. Fine. Usiamo solo cookie tecnici.";
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
