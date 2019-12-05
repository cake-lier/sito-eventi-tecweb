<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;

class DatabaseUsersManager extends DatabaseServiceManager {
    private const QUERY_ERROR = "An error occured while executing the query";
    private const RIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so";
    private const CUSTOMER_TYPE_CODE = "c";
    private const PROMOTER_TYPE_CODE = "p";

    /*
     *  Default constructor.
     */
    public __construct(mysqli $db) {
        DatabaseServiceManager::__construct($db);
    }
    /*
     * Inserts a new customer into the database. Throws an exception if something went wrong.
     */
    public function insertCustomer(string $email, string $password, string $profilePhoto, string $billingAddress,
                                    string $birthDate, string $birthplace, string $name, string $surname,
                                    string $username, string $telephone = null, string $currentAddress = null) {
        if (!$this->insertUser($email, $password, $profilePhoto, self::CUSTOMER_TYPE_CODE)) {
            throw new Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username,
                              telephone, currentAddress)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "sssssssss", $email, $billingAddress, $birthDate, $birthplace,
                                          $name, $surname, $username, $telephone, $currentAddress);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Inserts a new promoter into the database. Throws an exception if something went wrong.
     */
    public function insertPromoter(string $email, string $password, $profilePhoto, string $organizationName,
                                    string $VATid, string $website = null) {
        if (!$this->insertUser($email, $password, $profilePhoto, PROMOTER_TYPE_CODE)) {
            throw new Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "ssss", $email, $organizationName, $VATid, $website);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        } 
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Given the $email and the $plainPassword, checks if there is an account bound to them. If $email or
     * $plainPassword are wrong, it returns false, otherwise true. Throws an exception if something went wrong.
     */
    public function checkLogin(string $email, string $plainPassword) {
        $query = "SELECT email, password
                  FROM users
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        } 
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(self::QUERY_ERROR);
        }
        $userEmail = "";
        $dbPassword = "";
        // Gets the query result and saves it into the corresponding variables
        $stmt->bind_result($userEmail, $dbPassword);
        $stmt->fetch();
        $stmt->close();
        return password_verify($plainPassword, $dbPassword);
    }
    /*
     * Changes the password of the account with the given $email, only if the $oldPassword is correct and
     * the user is logged in. If the above conditions are not respected, returns false. Throws an exception if something
     * went wrong.
     */
    public function changePassword(string $email, string $oldPassword, string $newPassword) {
        try {
            if ($this->checkLogin($email, $oldPassword) && $this->isUserLoggedIn($email)) {
                $query = "UPDATE users
                          SET password = ?
                          WHERE email = ?";
                $stmt = $this->prepareBindExecute($query, "ss", password_hash($newPassword, PASSWORD_DEFAULT), $email);
                if ($stmt === false) {
                    throw new Exception(self::QUERY_ERROR);
                }
                $rows = $stmt->affected_rows;
                $stmt->close();
                if ($rows !== 1) {
                    throw new Exception(self::QUERY_ERROR);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /*
     * Changes the profile photo of the user logged in. Throws an exception if something went wrong.
     */
    public function changeProfilePhoto($photo) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE users
                  SET profilePhoto = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "bs", $photo, $email);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Changes the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function changeCustomerData(string $username, string $name, string $surname, string $birthDate,
                                       string $birthplace, string $billingAddress, string $currentAddress = null,
                                       string $telephone = null) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            throw new Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE customers
                  SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?,
                      billingAddress = ?, telephone = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "sssssssss", $username, $name, $surname, $birthDate, $birthplace,
                                          $currentAddress, $billingAddress, $telephone, $email);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Changes the the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function changePromoterData(string $website) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isPromoter($email)) {
            throw new Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE users
                  SET website = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "ss", $website, $email);
        if ($stmt === false) {
            throw new Exception(self::QUERY_ERROR);
        } 
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Returns a short version of the user with the given email.
     * The profile is returned only if:
     *      - it's a promoter's
     *      - it's a customer's, and the request is either from a promoter, an admin or the customer themselves
     *      - it's an admin's, and the request is from that admin
     * Otherwise, an exception is thrown.
     */
    public function getUserShortProfile(string $email) {
        if ($this->isPromoter($email)) {
            return getShortPromoterProfile($email);
        } else if ($this->isCustomer($email)) {
            $loggedEmail = $this->getLoggedUserEmail();
            if ($loggedEmail !== false && ($this->isPromoter($loggedEmail)
                                           || $this->isAdmin($loggedEmail)
                                           || $this->isUserLoggedIn($loggedEmail))) {
                return $this->getShortCustomerProfile($email);
            }
        } else if ($this->isAdmin($email) && $this->isUserLoggedIn($email)){
            return $this->getAdminProfile($email);
        }
        throw new Exception(self::PRIVILEGE_ERROR);
    }
    /*
     * Returns a long version of the logged in user profile. Throws an exception if something went wrong.
     */
    public function getLoggedUserLongProfile() {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new Exception(self::PRIVILEGE_ERROR);
        }
        if ($this->isPromoter($email)) {
            return $this->getLongPromoterProfile($email);
        } else if ($this->isCustomer($email)) {
            return $this->getLongCustomerProfile($email);
        } else if ($this->isAdmin($email)) {
            return $this->getAdminProfile($email);
        }
        throw new Exception(self::PRIVILEGE_ERROR);
    }
    /*
     * Deletes the account of the logged user, if the $password is correct, and returns true. Otherwise, it returns
     * false. Throws an exception if something went wrong.
     */
    public function deleteLoggedUser(string $password) {
        // Assuming there will be a cascade delete for customers and promoters tables
        $email = $this->getLoggedUserEmail();
        if (!$email) {
            throw new Exception(self::PRIVILEGE_ERROR);
        }
        try {
            if ($this->checkLogin($email, $password)) {
                $query = "DELETE FROM users
                          WHERE email = ?";
                $stmt = prepareBindExecute($query, "s", $email);
                if ($stmt === false) {
                    throw new Exception(self::QUERY_ERROR);
                } 
                $rows = $stmt->affected_rows;
                $stmt->close();
                if ($rows !== 1) {
                    throw new Exception(self::QUERY_ERROR);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /*
     * Inserts a new user into the database. Returns false if something went wrong.
     */
    private function insertUser(string $email, string $password, $profilePhoto, string $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "ssbs", $email, password_hash($password, PASSWORD_DEFAULT),
                                          $profilePhoto, $type);
        if ($stmt !== false) {
            $result = $stmt->affected_rows === 1;
            $stmt->close();
            return $result;
        }
        return false;
    }
    /*
     * Returns the short version of a customer profile, or false if an error occured.
     */
    private function getShortCustomerProfile(string $email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } 
        return false;
    }
    /*
     * Returns the long version of a customer profile, or false if an error occured.
     */
    private function getLongCustomerProfile(string $email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress,
                         telephone, email
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } 
        return false;
    }
    /*
     * Returns the short version of a promoter profile, or false if an error occured.
     */
    private function getShortPromoterProfile(string $email) {
        $query = "SELECT email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } 
        return false;
    }
    /*
     * Returns the long version of a customer profile, or false if an error occured.
     */
    private function getLongPromoterProfile(string $email) {
        $query = "SELECT email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoter p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        }
        return false;
    }
    /*
     * Returns the data inherent to an admin profile, or false if an error occured.
     */
    private function getAdminProfile(string $email) {
        $query = "SELECT email, profilePhoto
                  FROM users u
                  WHERE u.email = ?";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } 
        return false;
    }
}

?>
