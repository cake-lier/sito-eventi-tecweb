<header>
    <p>Riepilogo ordine</p><!--
    --><p>Pagamento</p><!--
    --><svg
       xmlns:dc="http://purl.org/dc/elements/1.1/"
       xmlns:cc="http://creativecommons.org/ns#"
       xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
       xmlns:svg="http://www.w3.org/2000/svg"
       xmlns="http://www.w3.org/2000/svg"
       xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
       xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
       width="100%"
       height="4em"
       viewBox="0 0 572 32"
       version="1.1"
       id="svg8"
       inkscape:version="0.92.4 5da689c313, 2019-01-14"
       sodipodi:docname="test.svg">
      <defs
         id="defs2" />
      <sodipodi:namedview
         id="base"
         pagecolor="#ffffff"
         bordercolor="#666666"
         borderopacity="1.0"
         inkscape:pageopacity="0.0"
         inkscape:pageshadow="2"
         inkscape:zoom="1.1575153"
         inkscape:cx="244.92646"
         inkscape:cy="974.68133"
         inkscape:document-units="mm"
         inkscape:current-layer="layer1"
         showgrid="false"
         inkscape:window-width="1920"
         inkscape:window-height="950"
         inkscape:window-x="0"
         inkscape:window-y="42"
         inkscape:window-maximized="1" />
      <metadata
         id="metadata5">
        <rdf:RDF>
          <cc:Work
             rdf:about="">
            <dc:format>image/svg+xml</dc:format>
            <dc:type
               rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
            <dc:title></dc:title>
          </cc:Work>
        </rdf:RDF>
      </metadata>
      <g
         inkscape:label="Layer 1"
         inkscape:groupmode="layer"
         id="layer1">
        <path
           style="fill:none;stroke:#79353f;stroke-width:4;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
           d="M 10,16 H 562"
           id="path3723"
           inkscape:connector-curvature="0"
           sodipodi:nodetypes="cc" />
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
