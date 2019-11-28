<?php

class DatabaseHelper{
    private $db;

    public function __construct($servername, $username, $password, $dbname){
        $this->db = new mysqli($servername, $username, $password, $dbname);
        if($this->db->connect_error){
            die("Connesione fallita al db");
        }
    }

    /****************************/
    /***** USERS FUNCTIONS *****/
    /**************************/
    // TODO: where is type definition?
    private function insertUser($email, $password, $profilePhoto, $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type, salt) 
                  VALUES (?, ?, ?, ?, ?)";
        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true)); // TODO: check if this is ok
        $password = hashPassword($password, $salt);
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssbis", $email, $password, $profilePhoto, $type, $salt);
        $stmt->execute();
        if ($stmt->affected_rows != 1) {
            $result = false;
        } else {
            $result = true;
        }
        $stmt->close();
        return $result;
    }

    public function insertCustomer($email, $password, $profilePhoto, 
                                   $billingAddress, $birthDate, 
                                   $birthplace, $name, $surname, 
                                   $username, $telephone = null, $currentAddress = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) { // TODO: the type is fixed
            $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username, telephone, currentAddress) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssssssis", $email, $billingAddress, $birthDate, $birthplace, $name, $surname, $username, $telephone, $currentAddress);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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

    public function insertPromoter($email, $password, $profilePhoto, $organizationName, $VATid, $website = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) { // TODO: the type is fixed
            $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                      VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssis", $email, $organizationName, $VATid, $website);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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
                if ($stmt2->affected_rows != 1) {
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
            if ($stmt->affected_rows != 1) {
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

    public function changeCustomerData($email, $username, $name, 
                                       $surname, $birthDate, $birthplace, 
                                       $billingAddress,  $currentAddress = null, $telephone = null) {
        if (isUserLoggedIn($email)) {
            $query = "UPDATE customers
                      SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?, billingAddress = ?, telephone = ?
                      WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssssssis", $username, $name, $surname, $birthDate, $birthplace, $currentAddress, $billingAddress, $telephone, $email);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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

    public function changePromoterData($email, $website) {
        if (isUserLoggedIn($email)) {
            $query = "UPDATE users
                      SET website = ?
                      WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ss", $website, $email);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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

    /*****************************/
    /***** EVENTS FUNCTIONS *****/
    /*****************************/
    public function getEventIds() {
        $query = "SELECT id
                  FROM events 
                  WHERE dateTime >= ?
                  ORDER BY (SELECT COUNT(customerEmail)
                            FROM subscriptions
                            WHERE id = eventId)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $website, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEventInfo($eventId) {
        // TODO: maybe it will be necessary to usa aliases for the fields
        $query = "SELECT e.id, e.name, e.place, e.dateTime, e.description, e.site, e.type, p.organizationName, e.seats - COUNT(*) as freeSeats
                  FROM events e, subscriptions s, promoters p
                  WHERE e.id = ?
                    AND e.id = s.eventId
                    AND e.promoterEmail = p.email
                  GROUP BY e.id";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $website, $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getEventsPlaces() {
        $query = "SELECT DISTINCT place
                  FROM events";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $website, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEventsTypes() {
        $query = "SELECT DISTINCT id, name
                  FROM eventCategories";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEventIdsFiltered($place, $date, $typeId, $free = true) {
        $condition = "";
        $bindings = "";
        if ($place != null) {
            $condition = "place = ?";
            $bindings = "s";
        }
        if ($date != null) {
            $condition = $condition == "" ? "" : $condition." AND ";
            $condition = $condition."date = ?";
            $bindings = $bindings."s";
        }
        if ($typeId != null) {
            $condition = $condition == "" ? "" : $condition." AND ";
            $condition = $condition."type = ?";
            $bindings = $bindings."i";
        }
        if ($free) {
            $condition = $condition == "" ? "" : $condition." AND ";
            $condition = $condition."seats > (SELECT COUNT(customerEmail) FROM subscriptions WHERE eventId = e.id)";
        }
        $query = "SELECT id
                  FROM events e
                  WHERE ".$condition;
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $place);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createEvent($name, $place, $dateTime, $seats, $description, $site = null) {
        if (isPromoter($_SESSION["email"])) { // only promoters can add events
            $query = "INSERT INTO events(name, place, dateTime, seats, description, site, promoterEmail)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssiisss", $name, $place, $dateTime, $seats, $description, $site, $_SESSION["email"]);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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

    public function getSubscribers($eventId) {
        $query = "SELECT customerEmail as email
                  FROM subscriptions
                  WHERE eventId = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSubscribedEvents() {
        // TODO: maybe it will be necessary to usa aliases for the fields
        $query = "SELECT e.id, e.name, e.place, e.dateTime, e.description, e.site, p.organizationName
                  FROM events e, subscriptions s
                  WHERE s.customerEmail = ?
                    AND e.id = s.eventId";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $_SESSION["email"]);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /***************************/
    /***** CART FUNCTIONS *****/
    /*************************/
    public function putTicketIntoCart($eventId) {
        if (isCustomer($_SESSION["email"])) {
            $query = "INSERT INTO carts(eventId, customerEmail)
                      SELECT ?, ?
                      FROM events
                      WHERE id IN (SELECT e.id 
                                   FROM events e 
                                   WHERE seats > (SELECT COUNT(customerEmail) 
                                                  FROM subscriptions 
                                                  WHERE eventId = e.id))";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("is", $eventId, $_SESSION["email"]);
            $stmt->execute();
            if ($stmt->affected_rows != 1) {
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

    public function removeTicketFromCart($eventId) {
        $query = "DELETE FROM carts
                  WHERE customerEmail = ?
                    AND eventId = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $_SESSION["email"], $eventId);
        $stmt->execute();
        if ($stmt->affected_rows != 1) {
            $result = false;
        } else {
            $result = true;
        }
        $stmt->close();
        return $result;
    }

    public function buyEventTicket($eventId) {
        $query = "INSERT INTO subscriptions(eventId, customerEmail)
                  SELECT ?, ?
                  FROM events
                  WHERE id IN (SELECT e.id 
                               FROM events e 
                               WHERE seats > (SELECT COUNT(customerEmail) 
                                              FROM subscriptions 
                                              WHERE eventId = e.id))";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $eventId, $_SESSION["email"]);
        $stmt->execute();
        if ($stmt->affected_rows != 1) {
            $result = false;
        } else {
            $result = true;
            removeTicketFromCart($eventId);
        }
        $stmt->close();
        return $result;
    }

    /************************************/
    /***** NOTIFICATIONS FUNCTIONS *****/
    /**********************************/

    /**********************/
    /***** UTILITIES *****/
    /********************/
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