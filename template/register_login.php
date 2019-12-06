<section>
    <header>
        <p>Login</p><p>Registrazione</p>
    </header>
    <form id="login_form" method="POST" action="login.php" autocomplete>
        <label for="email_login">Email:</label>
        <input type="text" id="email_login" name="email" />
        <label for="password_login">Password:</label>
        <input type="password" id="password_login" name="password" />
        <input type="submit" value="Login">
    </form>
    <form id="registration_form">
        <fieldset>
            <label for="check_customer">Cliente</label>
            <input type="radio" id="check_customer" name="registration_type" value="customer" checked/>
            <label for="check_promoter">Organizzatore</label>
            <input type="radio" id="check_promoter" name="registration_type" value="promoter" />
        </fieldset>
        <fieldset>
            <label for="email_register">Email:</label>
            <input type="text" id="email_register" name="email" required/>
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required/>
            <label for="password">Password:</label>
            <label for="surname">Cognome:</label>
            <input type="text" id="surname" name="surname" required/>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required/>
            <label for="birthdate">Data di nascita:</label>
            <input type="date" id="birthdate" name="birthdate" required/>
            <label for="birthplace">Luogo di nascita:</label>
            <input type="text" id="birthplace" name="birthplace" required/>
            <label for="profile_photo">Foto profilo:</label>
            <input type="required" id="profile_photo" name="profile_photo" required/>
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