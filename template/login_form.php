<form class="login" id="login_form" method="POST" action="login.php" autocomplete="on">
    <label for="email_login">Email:</label>
    <input class="login" type="email" id="email_login" name="email" required/>
    <label for="password_login">Password:</label>
    <input class="login" type="password" id="password_login" name="password" required/>
    <input type="hidden" id="landing_page" name="landing_page" value="<?php echo $templateParams["location"]; ?>" />
    <input type="submit" value="Login">
</form>
