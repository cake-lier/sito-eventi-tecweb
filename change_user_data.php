<?php
    require_once("bootstrap.php");
    header("Content-Type: application/json");
    // TODO: check if try catch are correctly innested
    if (isset($_SESSION["email"])) {
        if (isset($_FILES["profile_photo"]) && (file_exists($_FILES["profile_photo"]["tmp_name"]) || is_uploaded_file($_FILES["profile_photo"]["tmp_name"]))) {
            $imgData = encodeImg($_FILES["profile_photo"]["name"], $_FILES["profile_photo"]["tmp_name"]);
            try {
                $dbh->getUsersManager()->changeProfilePhoto($imgData);
            } catch (\Exception $e) {
                echo json_encode(array("resultMessage" => "Impossibile cambiare l'immagine profilo, riprovare più tardi"));
            }
        }
        if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            try {
                $dbh->getUsersManager()->changeCustomerData($_POST["username"], $_POST["name"], $_POST["surname"],
                                                            $_POST["birthDate"], $_POST["birthplace"], $_POST["billingAddress"],
                                                            $_POST["currentAddress"] === "" ? null : $_POST["currentAddress"],
                                                            $_POST["telephone"] === "" ? null : $_POST["telephone"]);
                echo json_encode(array("resultMessage" => "Dati modificati correttamente"));
            } catch (\Exception $e) {
                echo json_encode(array("resultMessage" => "Problemi con il database, riprovare più tardi"));
            }
        } else if ($dbh->getUsersManager()->isPromoter($_SESSION["email"])) {
            try {
                $dbh->getUsersManager()->changePromoterData($_POST["website"]);
                echo json_encode(array("resultMessage" => "Dati modificati correttamente"));
            } catch (\Exception $e) {
                echo json_encode(array("resultMessage" => $e->getMessage()));
            }
        }
    } else {
        echo json_encode(array("resultMessage" => "Occorre essere loggati per modificare i dati utente."));
    }
?>