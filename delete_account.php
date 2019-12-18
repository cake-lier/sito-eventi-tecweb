<?php
require_once "bootstrap.php";

header("Content-Type: application/json");
$data["result"] = "La password inserita è sbagliata oppure non è stato fatto il login";
$data["location"] = "";
try {
    if (isset($_POST["password"]) && $dbh->getUsersManager()->deleteLoggedUser($_POST["password"])) {
        session_unset();
        session_destroy();
        $data["location"] = "index.php";
        $data["result"] = "";
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $data["result"] = "Si è verificato un errore, si prega di riprovare più tardi";
}
echo json_encode($data);
?>