<section>
    <header>
        <p>Login</p><p>Registrazione</p>
    </header>
    <form id="login_form" method="POST" action="login.php" autocomplete>
        <?php
        if (isset($templateParams["loginError"])) { // FIXME: i don't work
            echo "<p>".$templateParams["loginError"]."</p>";
            unset($templateParams["loginError"]); // TODO: check this
        } ?>
        <label for="email_login">Email:</label>
        <input type="text" id="email_login" name="email" />
        <label for="password_login">Password:</label>
        <input type="password" id="password_login" name="password" />
        <input type="submit" value="Login">
    </form>
    <form id="registration_form" method="POST" action="registration.php">
        <!-- based on radio checked, class customer or class promoter must be hidden/required -->
        <fieldset>
            <label for="check_customer">Cliente</label>
            <input type="radio" id="check_customer" name="registration_type" value="customer" checked/>
            <label for="check_promoter">Organizzatore</label>
            <input type="radio" id="check_promoter" name="registration_type" value="promoter" />
        </fieldset>
        <fieldset>
            <!-- TODO: maybe add non strictly necessary fields? Like website for promoters, ... -->
            <!-- TODO: add also in registratio.php! -->
            <label for="email_register">Email:</label>
            <input type="text" id="email_register" name="email" required/>
            <label class="customer" for="name">Nome:</label>
            <input class="customer" type="text" id="name" name="name"/>
            <label class="customer" for="surname">Cognome:</label>
            <input class="customer" type="text" id="surname" name="surname"/>
            <label class="customer" for="username">Username:</label>
            <input class="customer" type="text" id="username" name="username"/>
            <label class="customer" for="birthdate">Data di nascita:</label>
            <input class="customer" type="date" id="birthdate" name="birthdate"/>
            <label class="customer" for="birthplace">Luogo di nascita:</label>
            <input class="customer" type="text" id="birthplace" name="birthplace"/>
            <label class="customer" for="billing">Indirizzo di fatturazione</label>
            <input class="customer" type="text" id="billing" name="billing"/>
            <label class="promoter" for="organization_name">Nome organizzazione:</label>
            <input class="promoter" type="text" id="organization_name" name="organization_name"/>
            <label class="promoter" for="vat_id">VATid:</label>
            <input class="promoter" type="text" id="vat_id" name="vat_id"/>
            <label for="profile_photo">Foto profilo:</label><!-- TODO: add requirements for profile photo -->
            <input type="file" id="profile_photo" name="profile_photo" required/>
            <label for="password_register">Password:</label>
            <input type="password" id="password_register" name="password" required/>
            <label for="password_register_repeat">Conferma password:</label>
            <input type="password" id="password_register_repeat" name="password" required/>
            <input type="submit" value="Registrati">
        </fieldset>
    </form>
    <p>Registrandoti accetti i nostri <a href="./info.php?type=termini">Termini di servizio</a> e 
    affermi di aver preso visione dell'<a href="./info.php?type=privacy">Informativa privacy</a>
</section>