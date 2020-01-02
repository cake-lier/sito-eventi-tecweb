<header>
    <p>Riepilogo ordine</p><!--
    --><p>Pagamento</p><!--
    --><svg
       xmlns="http://www.w3.org/2000/svg"
       width="100%"
       height="4em"
       viewBox="0 0 572 32"
       version="1.1"
       id="svg8">
      <defs id="defs2" />
      <g id="layer1">
        <path
           style="fill:none;stroke:#79353f;stroke-width:4;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
           d="M 10,16 H 562"
           id="path3723" />
        <circle
           id="path3725"
           cx="143"
           cy="16"
           style="stroke-width:0.15572274;fill:#79353f;fill-opacity:1"
           r="20" />
        <circle
           style="fill:#ffffff;fill-opacity:1;stroke:#79353f;stroke-width:0;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
           id="path4532"
           cx="143"
           cy="16"
           r="10" />
      </g>
    </svg>
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
