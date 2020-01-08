<?php
require_once "bootstrap.php";

header("Content-Type: application/json");
$data = ["resultMessage" => "Si è verificato un problema, si prega di riprovare più tardi"];
if (isset($_SESSION["email"])
    && isset($_POST["old_password"])
    && isset($_POST["new_password"])
    && isset($_POST["new_password_repeat"])) {
    if ($_POST["new_password"] === $_POST["new_password_repeat"]) {
        try {
            if ($dbh->getUsersManager()->changePassword($_SESSION["email"], $_POST["old_password"], $_POST["new_password"])) {
                $data["resultMessage"] = "Password cambiata correttamente";
            } else {
                $data["resultMessage"] = "Username o password sbagliati";
            }
        } catch (\Exception $e) {
            error_log($e->getMessage(), 3, LOG_FILE);
        }
    } else {
        $data["resultMessage"] = "Le due password devono coincidere";
    }
}
echo json_encode($data);
?>