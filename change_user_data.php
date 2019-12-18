<?php
require_once "bootstrap.php";

header("Content-Type: application/json");
try {
    if (isset($_SESSION["email"])) {
        if (isset($_FILES["profile_photo"])
            && (file_exists($_FILES["profile_photo"]["tmp_name"]) || is_uploaded_file($_FILES["profile_photo"]["tmp_name"]))) {
            $imgData = encodeImg($_FILES["profile_photo"]["name"], $_FILES["profile_photo"]["tmp_name"]);
            $dbh->getUsersManager()->changeProfilePhoto($imgData);
        }
        if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            $dbh->getUsersManager()->changeCustomerData($_POST["username"], $_POST["name"], $_POST["surname"],
                                                        $_POST["birthDate"], $_POST["birthplace"], $_POST["billingAddress"],
                                                        $_POST["currentAddress"] === "" ? null : $_POST["currentAddress"],
                                                        $_POST["telephone"] === "" ? null : $_POST["telephone"]);
        } else if ($dbh->getUsersManager()->isPromoter($_SESSION["email"])) {
            $dbh->getUsersManager()->changePromoterData($_POST["website"]);
        }
        echo json_encode(["resultMessage" => "Dati modificati correttamente"]);
    } else {
        echo json_encode(["resultMessage" => "Occorre essere loggati per modificare i dati utente."]);
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    echo json_encode(["resultMessage" => "Si è verificato un errore, si prega di riprovare più tardi."]);
}
?>