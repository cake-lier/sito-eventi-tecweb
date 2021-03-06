<form class="registration" id="registration_form" method="POST" action="registration.php" enctype="multipart/form-data">
    <!-- based on radio checked, class customer or class promoter must be hidden/required -->
    <fieldset>
        <label for="check_customer">Cliente</label>
        <input type="radio" id="check_customer" name="registration_type" value="customer" checked/>
        <label for="check_promoter">Organizzatore</label>
        <input type="radio" id="check_promoter" name="registration_type" value="promoter" />
    </fieldset>
    <fieldset>
        <label for="email_register">Email:</label>
        <input type="email" id="email_register" name="email" required/>
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
        <label class="customer" for="billing">Indirizzo di fatturazione:</label>
        <input class="customer" type="text" id="billing" name="billing"/>
        <label class="customer_non_req" for="current">Indirizzo corrente: </label>
        <input class="customer_non_req" type="text" id="current" name="current"/>
        <label class="customer_non_req" for="telephone">Telefono: </label>
        <input class="customer_non_req" type="text" id="telephone" name="telephone"/>
        <label for="profile_photo">Foto profilo <span>(formato png, jpg o jpeg, massimo 10MB)</span>:</label>
        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpg,image/png,image/jpeg" required/>
        <label for="password_register">Password:</label>
        <input type="password" id="password_register" name="password" required/>
        <label for="password_register_repeat">Conferma password:</label>
        <input type="password" id="password_register_repeat" name="password_repeat" required/>
    </fieldset>
    <input type="submit" value="Registrati">
</form>
<p class="registration">Registrandoti accetti i nostri <a href="./info.php?type=termini">Termini di servizio</a> e
affermi di aver preso visione dell'<a href="./info.php?type=privacy">Informativa privacy</a></p>