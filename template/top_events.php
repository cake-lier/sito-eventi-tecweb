<section class="top_event">
    <header>
        <h2>Il più popolare</h2>
    </header>
    <?php
        $event = $templateParams["mostPopularEvent"];
        require "template/event_tab.php";
    ?>
</section>
<a class="button_no_image" id="discover_link" href="search.php?keyword=">Scopri di più</a>
<section class="top_event">
    <header>
        <h2>Il prossimo in programma</h2>
    </header>
    <?php
        $event = $templateParams["mostRecentEvent"];
        require "template/event_tab.php";
    ?>
</section>