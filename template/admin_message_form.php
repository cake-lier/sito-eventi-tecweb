<p>Verrai ridirezionato alla home una volta inviato il messaggio!</p>
<form method="POST" action="send_admin_message.php">
    <header>
        <h2>Contatta gli admin</h2>
    </header>
    <section>
        <label for="message">Cosa desideri comunicare agli amministratori del sito?</label>
        <textarea id="message" name="message" rows=4 cols=50 required></textarea>
    </section>
    <footer>
        <input type="submit" value="Invia" />
    </footer>
</form>