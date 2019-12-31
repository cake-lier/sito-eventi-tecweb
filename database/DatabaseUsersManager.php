<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once("./database/DatabaseServiceManager.php");

class DatabaseUsersManager extends DatabaseServiceManager {
    private const QUERY_ERROR = "An error occured while executing the query";
    private const PRIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so";
    private const FILE_ERROR = "Cannot access file contents";
    private const CUSTOMER_TYPE_CODE = "c";
    private const PROMOTER_TYPE_CODE = "p";
    private const HASH_COST = 11;
    private const CONFIG_FILE = "database/config.txt";

    /*
     *  Default constructor.
     */
    public function __construct(\mysqli $db) {
        parent::__construct($db);
    }
    /*
     * Inserts a new customer into the database. Throws an exception if something went wrong.
     */
    public function insertCustomer(string $email, string $password, string $profilePhoto, string $billingAddress,
                                    string $birthDate, string $birthplace, string $name, string $surname,
                                    string $username, string $telephone = null, string $currentAddress = null) {
        if (!$this->insertUser($email, $password, $profilePhoto, self::CUSTOMER_TYPE_CODE)) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username,
                              telephone, currentAddress)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "sssssssss", $email, $billingAddress, $birthDate, $birthplace,
                                          $name, $surname, $username, $telephone, $currentAddress);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Inserts a new promoter into the database. Throws an exception if something went wrong.
     */
    public function insertPromoter(string $email, string $password, $profilePhoto, string $organizationName,
                                    string $VATid, string $website = null) {
        if (!$this->insertUser($email, $password, $profilePhoto, self::PROMOTER_TYPE_CODE)) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "ssss", $email, $organizationName, $VATid, $website);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        } 
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new \Exception(self::QUERY_ERROR);
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
            throw new \Exception(self::QUERY_ERROR);
        } 
        $stmt->store_result();
        switch ($stmt->num_rows) {
            case 0:
                return false;
                break;
            case 1:
                break;
            default:
                throw new \Exception(self::QUERY_ERROR);
                break;
        } 
        $userEmail = "";
        $dbPassword = "";
        // Gets the query result and saves it into the corresponding variables
        $stmt->bind_result($userEmail, $dbPassword);
        $stmt->fetch();
        $stmt->close();
        $pepper = file_get_contents(self::CONFIG_FILE);
        if ($pepper === false) {
            throw new \Exception(self::FILE_ERROR);
        }
        $pepperedPassword = hash_hmac("sha256", $plainPassword, $pepper);
        return password_verify($pepperedPassword, $dbPassword);
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
                $pepper = file_get_contents(self::CONFIG_FILE);
                if ($pepper === false) {
                    throw new \Exception(self::FILE_ERROR);
                }
                $pepperedPassword = hash_hmac("sha256", $newPassword, $pepper);
                $stmt = $this->prepareBindExecute($query, "ss", password_hash($pepperedPassword, 
                                                                              PASSWORD_BCRYPT,
                                                                              ["cost" => self::HASH_COST]),
                                                  $email);
                if ($stmt === false) {
                    throw new \Exception(self::QUERY_ERROR);
                }
                $rows = $stmt->affected_rows;
                $stmt->close();
                if ($rows !== 1) {
                    throw new \Exception(self::QUERY_ERROR);
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
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE users
                  SET profilePhoto = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "ss", $photo, $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new \Exception(self::QUERY_ERROR);
        }
        return;
    }
    /*
     * Changes the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function changeCustomerData(string $username, string $name, string $surname, string $birthDate,
                                       string $birthplace, string $billingAddress, string $currentAddress = null,
                                       string $telephone = null) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE customers
                  SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?,
                      billingAddress = ?, telephone = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "sssssssss", $username, $name, $surname, $birthDate, $birthplace,
                                          $currentAddress, $billingAddress, $telephone, $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1 && $rows !== 0) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Changes the the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function changePromoterData(string $website) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isPromoter($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE promoters
                  SET website = ?
                  WHERE email = ?";
        $stmt = $this->prepareBindExecute($query, "ss", $website, $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        } 
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1 && $rows !== 0) {
            throw new \Exception(self::QUERY_ERROR);
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
            return $this->getShortPromoterProfile($email);
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
        throw new \Exception(self::PRIVILEGE_ERROR);
    }
    /*
     * Returns a long version of the logged in user profile. Throws an exception if something went wrong.
     */
    public function getLoggedUserLongProfile() {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        if ($this->isPromoter($email)) {
            return $this->getLongPromoterProfile($email);
        } else if ($this->isCustomer($email)) {
            return $this->getLongCustomerProfile($email);
        } else if ($this->isAdmin($email)) {
            return $this->getAdminProfile($email);
        }
        throw new \Exception(self::PRIVILEGE_ERROR);
    }
    /*
     * Deletes the account of the logged user, if the $password is correct, and returns true. Otherwise, it returns
     * false. Throws an exception if something went wrong.
     */
    public function deleteLoggedUser(string $password) {
        // Assuming there will be a cascade delete for customers and promoters tables
        $email = $this->getLoggedUserEmail();
        if (!$email) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        try {
            if ($this->checkLogin($email, $password)) {
                $query = "DELETE FROM users
                          WHERE email = ?"; // TODO: set up on delete cascade in the db
                $stmt = $this->prepareBindExecute($query, "s", $email);
                if ($stmt === false) {
                    throw new \Exception(self::QUERY_ERROR);
                } 
                $rows = $stmt->affected_rows;
                $stmt->close();
                if ($rows !== 1) {
                    throw new \Exception(self::QUERY_ERROR);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
     /*
     * Checks if the given $email is associated to a promoter user. It returns false also in case of error.
     */
    public function isPromoter(string $email) {
        return parent::isPromoter($email);
    }
    /*
     * Checks if the given $email is associated to a customer user. It returns false also in case of error.
     */
    public function isCustomer(string $email) {
        return parent::isCustomer($email);
    }
    /*
     * Checks if the given $email is associated to an admin user. It returns false also in case of error.
     */
    public function isAdmin(string $email) {
        return parent::isAdmin($email);
    }
    /*
     * Returns a list of the names of the promoters' organizations.
     */
    public function getPromoters() {
        $query = "SELECT u.email, organizationName, profilePhoto
                  FROM promoters p, users u
                  WHERE u.email = p.email";
        // No risk of SQL injection
        $result = $this->query($query);
        if ($result === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        return $data;
    }
    /*
     * Gets the email of a promoter given the organization name.
     */
    public function getPromoterEmail(string $organizationName) {
        $query = "SELECT email
                  FROM promoters
                  WHERE organizationName = ?";
        $stmt = $this->prepareBindExecute($query, "s", $organizationName);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_assoc();;
            $stmt->close();
            return $result;
        }
        return false;
        
    }
    /*
     * Inserts a new user into the database. Returns false if something went wrong.
     */
    private function insertUser(string $email, string $password, $profilePhoto, string $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type)
                  VALUES (?, ?, ?, ?)";
        $pepper = file_get_contents(self::CONFIG_FILE);
        if ($pepper === false) {
            return false;
        }
        $pepperedPassword = hash_hmac("sha256", $password, $pepper);
        $stmt = $this->prepareBindExecute($query, "ssss", $email, 
                                          password_hash($pepperedPassword, PASSWORD_BCRYPT, ["cost" => self::HASH_COST]),
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
            $result = $stmt->get_result()->fetch_assoc();
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
                         telephone, u.email
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        }
        return false;
    }
    /*
     * Returns the short version of a promoter profile, or false if an error occured.
     */
    private function getShortPromoterProfile(string $email) {
        $query = "SELECT u.email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        } 
        return false;
    }
    /*
     * Returns the long version of a customer profile, or false if an error occured.
     */
    private function getLongPromoterProfile(string $email) {
        $query = "SELECT u.email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoters p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_assoc();
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
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        } 
        return false;
    }
}

?>
