<?php
require_once "bootstrap.php";

$error = true;
try {
    $dbh->getCartsManager()->buyLoggedUserTickets();
    $error = false;
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
}
header("location: cart.php" . ($error ? "?error" : ""));
?>