<?php
    foreach ($templateParams["events"] as $event) {
        require "template/event_tab.php";
    }
?>
<footer>
    <a class="button" id="previousPage"><img src="<?php echo IMG_DIR; ?>back.png" alt="Pagina precedente"/></a><!--
    --><a class="button" id="nextPage"><img src="<?php echo IMG_DIR; ?>next.png" alt="Pagina successiva"/></a>
</footer>
