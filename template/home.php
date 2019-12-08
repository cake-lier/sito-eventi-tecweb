<section>
    <h1>Non perderti neanche un evento che ami</h1>
    <img src="<?php echo IMG_DIR; ?>/home.jpg" alt="" />
    <form action="./search.php" method="get">
        <label for="ricerca"><img class="icon" src="<?php echo IMG_DIR; ?>/search.png" alt="ricerca" /></label>
        <input type="text" name="keyword" id="ricerca" />
    </form>
</section>
<section>
<?php
    // TODO: get most popular event
    echo '<a href=\'./search.php?keyword=""\'>Scopri di più</a>';
    // TODO: get most recent event
?>
</section>