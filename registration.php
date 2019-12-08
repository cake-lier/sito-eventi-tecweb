<?php
require_once "bootstrap.php";

$location = "login_page.php";
if (isset($_POST["email"])
    && isset($_FILES["profile_photo"])
    && isset($_POST["password"])) { // TODO: must check password_repeat too? Or js will do it?
        $email = $_POST["email"];
        $profile_photo = $_FILES["profile_photo"];
        $password = $_POST["password"];
        $imgData = addslashes(file_get_contents($_FILES["profile_photo"]["name"]));
        if ($_POST["registration_type"] === "customer"
                && isset($_POST["name"])
                && isset($_POST["surname"])
                && isset($_POST["username"])
                && isset($_POST["birthdate"])
                && isset($_POST["birthplace"])
                && isset($_POST["billing"])) {
            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $username = $_POST["username"];
            $birthdate = $_POST["birthdate"];
            $birthplace = $_POST["birthplace"];
            $billing = $_POST["billing"];
            try {
                $dbh->getUsersManager()->insertCustomer($email, $password, $imgData, 
                                                        $billing, $birthdate,$birthplace, 
                                                        $name, $surname, $username);
                $_SESSION["email"] = $email;
                $location = "index.php";
            } catch (\Exception $e) {
                $error = "Problema con il database";
            }                                    
        } else if ($_POST["registration_type"] === "promoter"
                    && isset($_POST["organization_name"])
                    && isset($_POST["vat_id"])) {
            $imgData = addslashes(file_get_contents($_FILES["profile_photo"]["name"]));
            $name = $_POST["organization_name"];
            $vat = $_POST["vat_id"];
            try {
                $dbh->getUsersManager()->insertPromoter($email, $password, $imgData, 
                                                        $name, $vat, null);
                $_SESSION["email"] = $email;
                $location = "index.php";
            } catch (\Exception $e) {
                $error = "Problema con il database";
            }                                
        }
} else {

}

header("location: ".$location);
?>