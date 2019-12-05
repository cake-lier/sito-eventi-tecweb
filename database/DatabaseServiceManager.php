<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;

/*
 * This class represents an abstract service manager which uses a database connection to offer its services.
 */
abstract class DatabaseServiceManager {
    private const CUSTOMER_TYPE_CODE = "c";
    private const PROMOTER_TYPE_CODE = "p";
    private const ADMIN_TYPE_CODE = "a";

    private mysqli $db;
    /*
     * Default constructor.
     */
    protected __construct(mysqli $db) {
        $this->db = $db;
    }
    /*
     * Checks if the given $email is associated to a promoter user. It returns false also in case of error.
     */
    protected function isPromoter(string $email) {
        return $this->isUserType($email, self::PROMOTER_TYPE_CODE);
    }
    /*
     * Checks if the given $email is associated to a customer user. It returns false also in case of error.
     */
    protected function isCustomer(string $email) {
        return $this->isUserType($email, self::CUSTOMER_TYPE_CODE);
    }
    /*
     * Checks if the given $email is associated to an admin user. It returns false also in case of error.
     */
    protected function isAdmin(string $email) {
        return $this->isUserType($email, self::ADMIN_TYPE_CODE);
    }
    /*
     * Checks if the user on which the current operation is being done is the user that is currently logged in.
     */
    protected function isUserLoggedIn(string $email) {
        return $_SESSION["email"] === $email;
    }
    /*
     * Prepares a statement from the given query, and binds the arguments to the statement using the given binding
     * string. Then, the statement is executed and returned for use. If something fails, false is returned.
     */
    protected function prepareBindExecute(string $query, string $bindings, ...$arguments) {
        $stmt = $this->db->prepare($query);
        if ($stmt !== false) {
            $stmt->bind_param($bindings, $arguments);
            $stmt->execute();
        }
        return $stmt;
    }
    /*
     * Returns the results of a query made on the database using the passed $queryString.
     */
    protected function query(string $queryString) {
        return $this->db->query($queryString);
    }
    /*
     * Returns the email of the currently logged user, false if no user is logged.
     */
    protected function getLoggedUserEmail() {
        return isset($_SESSION["email"]) ? $_SESSION["email"] : false;
    }
    /*
     * Checks if the given $email is associated to an user of the given $type. It returns false also in case of error.
     */
    private function isUserType(string $email, string $type) {
        $query = "SELECT *
                  FROM users
                  WHERE email = ? AND type = ?";
        $stmt = $this->prepareBindExecute($query, "ss", $email, $type);
        return ($stmt !== false && $stmt->fetch() != null);
    }
}

?> 
