<section>
    <h1>Non perderti neanche un evento che ami</h1>
    <img src="<?php echo IMG_DIR; ?>/home.jpg" alt="" />
    <form action="./search.php" method="get">
        <label for="ricerca"><img class="icon" src="<?php echo IMG_DIR; ?>/search.png" alt="ricerca" /></label>
        <input type="text" name="keyword" id="ricerca" />
    </form>
</section>
<?php

require "template/top_events.php";

?>