<form method="POST">
    <header>
        <h2>Crea un nuovo evento</h2>
    </header>
    <section class="general_section">
        <label for="name">Nome: </label>
        <input type="text" id="name" name="event_name" required />
        <label for="place">Luogo: </label>
        <input type="text" id="place" name="place" required />
        <label for="dateTime">Data e ora: </label>
        <input type="datetime-local" id="dateTime" name="dateTime" required />
        <label for="description">Descrizione: </label>
        <textarea id="description" name="description" rows=4 cols=50 required></textarea>
        <label for="website">Sito web: </label>
        <input type="text" id="website" name="website" />
        <!-- TODO: event categories as in DA -->
    </section>
    <button type="button">Aggiungi categoria biglietti</button>
    <footer>
        <input type="submit" value="Crea" />
    </footer>
</form>