<form id="login_form" method="POST" action="login.php" autocomplete>
        <?php
        if (isset($_SESSION["loginError"])) {
            echo "<p>".$_SESSION["loginError"]."</p>";
            unset($_SESSION["loginError"]);
        } ?>
        <label for="email_login">Email:</label>
        <input type="text" id="email_login" name="email" />
        <label for="password_login">Password:</label>
        <input type="password" id="password_login" name="password" />
        <input type="submit" value="Login">
    </form>