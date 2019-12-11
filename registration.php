<?php
require_once "bootstrap.php";

$location = "login_page.php";
if (isset($_POST["email"])
    && (file_exists($_FILES["profile_photo"]["tmp_name"]) || is_uploaded_file($_FILES["profile_photo"]["tmp_name"]))
    && isset($_POST["password"])
    && isset($_POST["password_repeat"])
    && $_POST["password"] === $_POST["password_repeat"]) { // TODO: must check password_repeat too? Or js will do it?
        $email = $_POST["email"];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            $profile_photo = $_FILES["profile_photo"];
            $password = $_POST["password"];
            $imgData = encodeImg($_FILES["profile_photo"]["name"], $_FILES["profile_photo"]["tmp_name"]);
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
                    $_SESSION["registrationError"] = "Problema con il database";
                }                                    
            } else if ($_POST["registration_type"] === "promoter"
                        && isset($_POST["organization_name"])
                        && isset($_POST["vat_id"])) {
                $name = $_POST["organization_name"];
                $vat = $_POST["vat_id"];
                try {
                    $dbh->getUsersManager()->insertPromoter($email, $password, $imgData, 
                                                            $name, $vat, null);
                    $_SESSION["email"] = $email;
                    $location = "index.php";
                } catch (\Exception $e) {
                    $_SESSION["registrationError"] = "Problema con il database";
                }                                
            }
        } else {
            $_SESSION["registrationError"] = "Mail non valida";
        }
} else if ($_POST["password"] !== $_POST["password_repeat"]) {
    $_SESSION["registrationError"] = "I campi password e conferma password sono diversi!";
}

header("location: ".$location);
?>