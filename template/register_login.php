<?php
    require_once "bootstrap.php";
?>
<section id="register_login">
    <header>
        <button class="selected" type="button" id="login_button">Login</button><button type="button" id="registration_button">Registrazione</button>
    </header>
    <?php
        if (isset($_SESSION["registrationError"])) {
            echo "<p>" . $_SESSION["registrationError"] . "</p>";
            unset($_SESSION["registrationError"]);
        }
    ?>
    <?php
        if (isset($_SESSION["loginError"])) {
            echo "<p>" . $_SESSION["loginError"] . "</p>";
            unset($_SESSION["loginError"]);
        }
    ?>
    <?php
        require "template/login_form.php";
        require "template/registration_form.php";
    ?>
</section>