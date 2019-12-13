<form id="login_form" method="POST" action="login.php" autocomplete>
    <?php
        if (isset($_SESSION["loginError"])) {
            echo "<p>" . $_SESSION["loginError"] . "</p>";
            unset($_SESSION["loginError"]);
        }
    ?>
        <label for="email_login">Email:</label>
        <input class="login" type="text" id="email_login" name="email" required/>
        <label for="password_login">Password:</label>
        <input class="login" type="password" id="password_login" name="password" required/>
        <input type="hidden" id="landing_page" name="landing_page" value="<?php echo $templateParams["location"]; ?>" />
        <input type="submit" value="Login">
    </form>
