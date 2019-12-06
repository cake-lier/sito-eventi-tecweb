<section>
    <?php
        require("template/search_form.php");
    ?>
</section>
<section>
    <?php
        if (isset($_GET["eventId"])) {
            // a specific event was requested
            // TODO: get data from db
        } else {
            header("location: ./search.php?keyword=\"\"");
        }
    ?>
</section>