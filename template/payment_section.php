<header>
    <!--TODO: Use of illustrator for svg drawing-->
</header>
<section>
    <section>
        <h1>Dati di fatturazione:</h1>
        <p><?php echo $templateParams["user"]["name"] . " " . $templateParams["user"]["surname"]; ?></p>
        <p><?php echo $templateParams["user"]["billingAddress"]; ?></p>
    </section>
    <p>Totale <?php echo $templateParams["total"]; ?>€</p>
    <section id="payment_types">
        <h1>Seleziona un tipo di pagamento:</h1>
        <ul>
            <li><img src="img/18app_logo.png" alt="18app" /></li><!--
            --><li><img src="img/mastercard_logo.png" alt="mastercard" /></li><!--
            --><li><img src="img/visa_logo.png" alt="visa" /></li><!--
            --><li><img src="img/paypal_logo.png" alt="paypal" /></li><!--
            --><li><img src="img/postepay_logo.png" alt="postepay" /></li>
        </ul>
    </section>
    <a class="button_no_image" href="#" id="buy_button">Acquista</a>  
</section>
<footer>
    <p>Completando l'acquisto accetti i nostri <strong><a href="info.php?type=termini">Termini di Servizio</a></strong> e affermi <!--
    -->di aver preso visione dell'<strong><a href="info.php?type=privacy"></strong>Informativa privacy</a>.</p>
</footer> 
