<?php
require_once "bootstrap.php";

$_SESSION["paymentError"] = true;
try {
    $dbh->getCartsManager()->buyLoggedUserTickets();
    unset($_SESSION["paymentError"]);
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
}
header("location: cart.php");
?>