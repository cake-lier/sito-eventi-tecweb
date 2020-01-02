<?php
    foreach ($templateParams["events"] as $event) {
        require "template/event_tab.php";
    }
?>
<footer>
    <a class="button" id="previous_page"><img src="<?php echo IMG_DIR; ?>back.png" alt="Pagina precedente"/></a><!--
    --><select id="event_count">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
    </select><!--
    --><a class="button" id="next_page"><img src="<?php echo IMG_DIR; ?>next.png" alt="Pagina successiva"/></a>
</footer>
