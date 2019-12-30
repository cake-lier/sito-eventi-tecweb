<section class="home">
    <h1>Non perderti neanche un evento che ami</h1>
</section>
<section class="home">
    <form id="main_search" action="search.php" method="get">
        <label for="keyword"><img class="icon" src="<?php echo IMG_DIR; ?>search.png" alt="ricerca" /></label>
        <input type="text" name="keyword" id="keyword" />
    </form>
</section>
<section id="main_section">
    <?php
        if (isset($templateParams["mostPopularEvent"]) && isset($templateParams["mostRecentEvent"])) {
            require "template/top_events.php";
        } else {
            require "template/no_events.php";
        }
    ?>
</section>