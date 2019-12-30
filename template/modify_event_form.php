<section>
    <form method="POST">
        <h2>Modifica "<?php echo $templateParams["event"]["name"]; ?>"</h2>
        <label for="dateTime">Data e ora: </label>
        <input type="datetime-local" max="9999-12-31T23:59" id="dateTime" name="dateTime" 
               value="<?php echo $templateParams["event"]["dateTime"]; ?>" required />
        <label for="message">Messaggio per gli iscritti: </label>
        <textarea id="message" name="message" rows=4 cols=50 required></textarea>
        <input type="hidden" name="id" value="<?php echo $templateParams["event"]["id"]; ?>" />
        <input class="button_no_image" type="submit" value="Modifica" />
    </form>
</section>