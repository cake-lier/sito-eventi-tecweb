<nav id="user_area_menu">
    <a href="#"><img src="img/menu.png" alt="Chiudi menu area utente"/></a>
    <ul>
        <li class="selected" id="notifications_button">Notifiche</li>
        <li id="user_area_button">Dati personali</li>
        <li id="change_password_button">Cambia password</li>
        <li id="change_data_button">Modifica dati</li>
        <?php
            if ($templateParams["isCustomer"]):
        ?>
            <li id="notifications_pref_button">Impostazioni notifiche</li>
        <?php
            endif;
            if (!$templateParams["isAdmin"]):
        ?>
            <li id="events_button">I miei eventi</li>
        <?php
            endif;
        ?>
        <li id="delete_account_button">Chiudi account</li>
    </ul>
</nav>