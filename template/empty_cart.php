<section>
    <p>Non hai ancora inserito niente nel tuo carrello, forse potrebbero interessarti questi eventi...</p>
</section>
<section>
    <?php if (isset($templateParams["mostPopularEvent"]) && isset($templateParams["mostRecentEvent"])) {
            require "template/top_events.php";
          } else {
            require "template/no_events.php";
          } 
    ?>
</section>