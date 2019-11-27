<?php

class DatabaseHelper{
    private $db;

    public function __construct($servername, $username, $password, $dbname){
        $this->db = new mysqli($servername, $username, $password, $dbname);
        if($this->db->connect_error){
            die("Connesione fallita al db");
        }
    }

    // TODO: how password should be managed? Look on iol
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
        $stmt->store_result();
        $stmt->bind_result($userEmail, $db_password, $salt); // recupera il risultato della query e lo memorizza nelle relative variabili.
        $stmt->fetch();
        if ($stmt->num_rows == 1) {
            if ($db_password == hashPassword($plainPassword, $salt)) {
                return true;
            }
        }
        return false;
    }

    private function hashPassword($password, $salt) {
        return hash('sha512', $password.$salt);
    }
}
?>