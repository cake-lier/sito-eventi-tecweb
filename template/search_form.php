<section id="search_section">
    <form action="./search.php" method="get">
        <fieldset id="general_search">
            <label for="keyword"><img src="<?php echo IMG_DIR; ?>search.png" alt="ricerca per parole chiave" /></label><!--
            --><input type="text" name="keyword" id="keyword" /><!--
            --><button type="button" id="filters_button">Filtri</button>
        </fieldset>
        <fieldset id="specific_search">
            <label for="tags"><img src="<?php echo IMG_DIR; ?>hashtag.png" alt="ricerca per tag" /></label><!--
            --><input type="text" name="tags" id="tags" /><!--
            --><label for="luogo">Luogo:</label><!--
            --><select id="luogo" name="place">
                <option value="">Seleziona un luogo</option>
                <?php
                    foreach($templateParams["places"] as $place):
                ?>
                    <option value="<?php echo $place; ?>"><?php echo $place; ?></option>
                <?php
                    endforeach;
                ?>
            </select><!--
            --><label for="promoter">Organizzatore:</label><!--
            --><select id="promoter" name="promoter">
                <option value="">Seleziona un promoter</option>
                    <?php
                        foreach($templateParams["promoters"] as $promoter):
                    ?>
                        <option value="<?php echo $promoter; ?>"><?php echo $promoter; ?></option>
                    <?php
                        endforeach;
                    ?>
            </select><!--
            --><label for="data">Data:</label><!--
            --><input type="date" id="data" name="date" /><!--
            --><label for="posti">Posti liberi:</label><!--
            --><input type="checkbox" id="posti" name="posti" value="free" checked />
        </fieldset>
        <input class="button_no_image" type="submit" id="search" name="search" value="Cerca"/>
    </form>
</section>
<?php
    if(isset($templateParams["searchSecondSection"])) {
        require $templateParams["searchSecondSection"];
    }
?>