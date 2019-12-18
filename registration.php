<?php
require_once "bootstrap.php";

$location = "login_page.php";
if (isset($_POST["email"])
    && (file_exists($_FILES["profile_photo"]["tmp_name"]) || is_uploaded_file($_FILES["profile_photo"]["tmp_name"]))
    && isset($_POST["password"])
    && isset($_POST["password_repeat"])
    && $_POST["password"] === $_POST["password_repeat"]) {
    $email = $_POST["email"];
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
        $profile_photo = $_FILES["profile_photo"];
        $password = $_POST["password"];
        // Roughly estimate of the maximum size for a file which, after being encoded in Base64, occupies at maximum 16 MiB
        if ($_FILES["profile_photo"]["size"] < 12000000) {
            $imgData = encodeImg($_FILES["profile_photo"]["name"], $_FILES["profile_photo"]["tmp_name"]);
            if ($_POST["registration_type"] === "customer"
                && isset($_POST["name"])
                && isset($_POST["surname"])
                && isset($_POST["username"])
                && isset($_POST["birthdate"])
                && isset($_POST["birthplace"])
                && isset($_POST["billing"])
                && $imgData !== false) {
                $name = $_POST["name"];
                $surname = $_POST["surname"];
                $username = $_POST["username"];
                $birthdate = $_POST["birthdate"];
                $birthplace = $_POST["birthplace"];
                $billing = $_POST["billing"];
                $current = isset($_POST["current"]) ? $_POST["current"] : null;
                $telephone = isset($_POST["telephone"]) ? $_POST["telephone"] : null;
                try {
                    $dbh->getUsersManager()->insertCustomer($email, $password, $imgData, $billing, $birthdate,$birthplace, $name,
                                                            $surname, $username, $current, $telephone);
                    $_SESSION["email"] = $email;
                    $location = "index.php";
                } catch (\Exception $e) {
                    $_SESSION["registrationError"] = "Problema con il database";
                }                                    
            } else if ($_POST["registration_type"] === "promoter"
                       && isset($_POST["organization_name"])
                       && isset($_POST["vat_id"])
                       && $imgData !== false) {
                $name = $_POST["organization_name"];
                $vat = $_POST["vat_id"];
                $website = isset($_POST["website"]) ? $_POST["website"] : null;
                try {
                    $dbh->getUsersManager()->insertPromoter($email, $password, $imgData, $name, $vat, $website);
                    $_SESSION["email"] = $email;
                    $location = "index.php";
                } catch (\Exception $e) {
                    $_SESSION["registrationError"] = "Problema con il database";
                }                                
            } else if ($imgData === false) {
                $_SESSION["registrationError"] = "L'immagine di profilo non va bene!";
            }
        } else {
            $_SESSION["registrationError"] = "L'immagine di profilo é troppo grande!";
        }
    } else {
        $_SESSION["registrationError"] = "Mail non valida";
    }
} else if ($_POST["password"] !== $_POST["password_repeat"]) {
    $_SESSION["registrationError"] = "I campi password e conferma password sono diversi.";
}
header("location: " . $location);
?>