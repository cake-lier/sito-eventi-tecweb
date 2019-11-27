<?php

class DatabaseHelper{
    private $db;

    public function __construct($servername, $username, $password, $dbname){
        $this->db = new mysqli($servername, $username, $password, $dbname);
        if($this->db->connect_error){
            die("Connesione fallita al db");
        }
    }

    // TODO: where is type definition?
    private function insertUser($email, $password, $profilePhoto, $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type, salt) 
                  VALUES (?, ?, ?, ?, ?)";
        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true)); // TODO: check if this is ok
        $password = hashPassword($password, $salt);
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssbis", $email, $password, $profilePhoto, $type, $salt);
        $stmt->execute();
        if ($stmt->affected_rows === -1 || $stmt->affected_rows === 0) {
            $result = false;
        } else {
            $result = true;
        }
        $stmt->close();
        return $result;
    }

    public function insertCustomer($email, $password, $profilePhoto, 
                                   $type, $billingAddress, $birthDate, 
                                   $birthplace, $name, $surname, 
                                   $username, $telephone = null, $currentAddress = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) {
            $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username, telephone, currentAddress) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssssssis", $email, $billingAddress, $birthDate, $birthplace, $name, $surname, $username, $telephone, $currentAddress);
            $stmt->execute();
            if ($stmt->affected_rows === -1 || $stmt->affected_rows === 0) {
                $result = false;
            } else {
                $result = true;
            }
            $stmt->close();
            return $result;
        }
    }

    public function insertPromoter($email, $password, $profilePhoto, $type, $organizationName, $VATid, $website = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) {
            $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                      VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssis", $email, $organizationName, $VATid, $website);
            $stmt->execute();
            if ($stmt->affected_rows === -1 || $stmt->affected_rows === 0) {
                $result = false;
            } else {
                $result = true;
            }
            $stmt->close();
            return $result;
        }
    }

    public function checkLogin($email, $plainPassword) {
        $query = "SELECT email, password, salt
                  FROM users
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userEmail, $db_password, $salt); // recupera il risultato della query e lo memorizza nelle relative variabili.
        $stmt->fetch();
        $result = false;
        if ($stmt->num_rows == 1) {
            if ($db_password == hashPassword($plainPassword, $salt)) {
                $result = true;
            }
        }
        $stmt->close();
        return $result;
    }
    
    public function changePassword($email, $oldPassword, $newPassword) {
        if (checkLogin($email, $oldPassword) && isUserLoggedIn($email)) {
            $query = "SELECT email, salt
                      FROM users
                      WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($userEmail, $salt);
            if ($stmt->fetch()) {
                // should be just one result
                $query2 = "UPDATE users
                           SET password = ?
                           WHERE email = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("ss", hashPassword($newPassword, $salt), $email);
                $stmt2->execute();
                if ($stmt2->affected_rows === -1 || $stmt2->affected_rows === 0) {
                    $result = false;
                } else {
                    $result = true;
                }
                $stmt->close();
                $stmt2->close();
                return $result;
            }
        } else {
            return false;
        }
    }

    public function changeProfilePhoto($email, $photo) {
        if (isUserLoggedIn($email)) {
            $query = "UPDATE users
                      SET profilePhoto = ?
                      WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("bs", $photo, $email);
            $stmt->execute();
            if ($stmt2->affected_rows === -1 || $stmt2->affected_rows === 0) {
                $result = false;
            } else {
                $result = true;
            }
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    }

    public function getUserShortProfile($email) {
        if (isPromoter($email)) {
            return getShortPromoterProfile($email);
        } else if (isCustomer($email)) {
            if (isPromoter($_SESSION["email"]) || isAdmin($_SESSION["email"]) || isUserLoggedIn($email)) { // TODO: check this
                return getShortCustomerProfile($email);
            }
        } else {
            return getAdminProfile($email);
        }
    }

    public function getUserLongProfile($email) {
        if (isUserLoggedIn($email)) {
            if (isPromoter($email)) {
                return getLongPromoterProfile($email);
            } else if (isCustomer($email)) {
                return getShortCustomerProfile($email);
            } else {
                getAdminProfile($email);
            }
        }
    }

    private function getShortCustomerProfile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto
                  FROM users u, customers c 
                  WHERE u.email = ? 
                      AND u.email = c.email";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getLongCustomerProfile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress, telephone, email
                  FROM users u, customers c 
                  WHERE u.email = ? 
                      AND u.email = c.email";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getShortPromoterProfile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? 
                      AND u.email = p.email";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getLongPromoterProfile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoter p 
                  WHERE u.email = ? 
                      AND u.email = p.email";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getAdminProfile($email) {
        $query = "SELECT email, profilePhoto
                  FROM users u
                  WHERE u.email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function hashPassword($password, $salt) {
        return hash('sha512', $password.$salt);
    }

    private function isUserType($email, $type) {
        $query = "SELECT *
                  FROM users
                  WHERE email = ?
                    AND type = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $email, $type);
        $stmt->execute();
        return ($stmt->fetch() != null);
    }

    private function isPromoter($email) {
        // TODO: change to use type
        /* return isUserType($email, TYPE); */
        $query = "SELECT *
                  FROM promoters
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return ($stmt->fetch() != null); // if there is no promoter with the given email returns false
    }

    private function isCustomer($email) {
        // TODO: change to use type
        /* return isUserType($email, TYPE); */
        $query = "SELECT *
                  FROM customers
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return ($stmt->fetch() != null); // if there is no promoter with the given email returns false
    }

    private function isAdmin($email) {
        // TODO: change to use type
        /* return isUserType($email, TYPE); */
        $query = "SELECT *
                  FROM administrators
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return ($stmt->fetch() != null); // if there is no promoter with the given email returns false
    }
    
    private function isUserLoggedIn($email) {
        return $_SESSION["email"] == $email;
    }
}
?>