<section>
    <?php
        foreach ($templateParams["events"] as $event) {
            require "template/event_tab.php";
        }
    ?>
</section>
<footer>
<button id="previousPage"><img src="<?php echo IMG_DIR; ?>back.png" alt="Pagina precedente"/></button>
<button id="nextPage"><img src="<?php echo IMG_DIR; ?>next.png" alt="Pagina successiva"/></button>
</footer>
