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
        <input type="datetime-local" max="9999-12-31T23:59" id="dateTime" name="dateTime" required />
        <label for="description">Descrizione: </label>
        <textarea id="description" name="description" rows=4 cols=50 required></textarea>
        <label for="website">Sito web: </label>
        <input type="text" id="website" name="website" />
        <label for="categories">Categorie: </label>
        <input type="text" id="categories" name="categories" />
    </section>
    <section class="seat_category_section">
        <label for="sCatName_0">Nome categoria: </label>
        <input type="text" id="sCatName_0" name="name" required />
        <label for="sCatQuantity_0">Quantità biglietti categoria: </label>
        <input type="number" step="1" id="sCatQuantity_0" name="seats" required />
        <label for="sCatPrice_0">Prezzo biglietti categoria: </label>
        <input type="number" step="any" id="sCatPrice_0" name="price" required />
    </section>
    <button type="button">Aggiungi categoria biglietti</button>
    <footer>
        <input type="submit" value="Crea" />
    </footer>
</form>