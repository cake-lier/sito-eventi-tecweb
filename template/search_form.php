<form action="./search.php" method="get">
        <label for="ricerca"><img src="<?php echo IMG_DIR; ?>/search.png" alt="ricerca" /></label>
        <input type="text" name="keyword" id="ricerca" />
        <label for="luogo">Luogo:</label>
        <select id="luogo" name="place">
            <?php
                // TODO: insert places from database
                $places = array(); // TODO: substitute with database call
                foreach($places as $place):
            ?>
                <option value="<?php echo $place; ?>"><?php echo $place; ?></option>
            <?php endforeach;?>
        </select>
        <label for="data">Data:</label>
        <input type="date" id="data" name="date" />
        <label for="posti">Posti liberi:</label>
        <input type="checkbox" name="posti" value="free" checked />
</form>