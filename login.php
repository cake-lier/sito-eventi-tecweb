<?php
require_once "bootstrap.php";

$location = "index.php";
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["landing_page"])) {
    $location = $_POST["landing_page"];
    try {
        $loginResult = $dbh->getUsersManager()->checkLogin($_POST["email"], $_POST["password"]);
        if ($loginResult) {
            $_SESSION["email"] = $_POST["email"];
            $location = "index.php";
        } else {
            $_SESSION["loginError"] = "Username o password errata";
        }
    } catch (\Exception $e) {
        $_SESSION["loginError"] = "Sei sicuro di essere registrato?";
    }
}

header("location: " . $location);
?>
