<?php

// TODO: check if $_SESSION["email"] is set
// TODO: check if prepare() returns false
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
    /*
     * Inserts a new user into the database, returns true if everything went well, false otherwise.
     */
    private function insertUser($email, $password, $profilePhoto, $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type, salt) 
                  VALUES (?, ?, ?, ?, ?)";
        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true)); // TODO: check if this is ok
        $password = hashPassword($password, $salt);
        $stmt = prepareBindExecute($query, "ssbis", $email, $password, $profilePhoto, $type, $salt);
        if ($stmt !== false) {
            $result = $stmt->affected_rows != 1;
            $stmt->close();
            return $result;
        }
        return false;
    }

    /*
     * Inserts a new customer into the database, returns true if everything went well, false otherwise.
     */
    public function insertCustomer($email, $password, $profilePhoto, 
                                   $billingAddress, $birthDate, 
                                   $birthplace, $name, $surname, 
                                   $username, $telephone = null, $currentAddress = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) { // TODO: the type is fixed
            $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username, telephone, currentAddress) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = prepareBindExecute($query, "sssssssis", $email, 
                                       $billingAddress, $birthDate, $birthplace, 
                                       $name, $surname, $username, 
                                       $telephone, $currentAddress);
            if ($stmt !== false) {
                $result = $stmt->affected_rows != 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Inserts a new promoter into the database, returns true if everything went well, false otherwise.
     */
    public function insertPromoter($email, $password, $profilePhoto, $organizationName, $VATid, $website = null) {
        if (insertUser($email, $password, $profilePhoto, $type)) { // TODO: the type is fixed
            $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                      VALUES (?, ?, ?, ?)";
            $stmt = prepareBindExecute($query, "ssis", $email, $organizationName, $VATid, $website);
            if ($stmt !== false) {
                $result = $stmt->affected_rows != 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Given the $email and the $plainPassword, checks if there is an account bound to them.
     * If there are problems executing the query, or if $email or $plainPassword are wrong, 
     * returns false.
     */
    public function checkLogin($email, $plainPassword) {
        $query = "SELECT email, password, salt
                  FROM users
                  WHERE email = ?";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $userEmail = "";
            $db_password = "";
            $salt = "";
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
        return false;
    }
    
    /*
     * Changes the password of the account with the given $email, only if the $oldPassword is correct and
     * the user is logged in. If problems arise or if the above conditions are not respected, returns false.
     */
    public function changePassword($email, $oldPassword, $newPassword) {
        if (checkLogin($email, $oldPassword) && isUserLoggedIn($email)) {
            $query = "SELECT email, salt
                      FROM users
                      WHERE email = ?";
            $stmt = prepareBindExecute($query, "s", $email);
            if ($stmt !== false) {
                $userEmail = "";
                $salt = "";
                $stmt->bind_result($userEmail, $salt);
                if ($stmt->fetch()) {
                    // should be just one result
                    $query2 = "UPDATE users
                               SET password = ?
                               WHERE email = ?";
                    $stmt2 = prepareBindExecute($query2, "ss", hashPassword($newPassword, $salt), $email);
                    $stmt->close();
                    if ($stmt2 !== false) {
                        $result = ($stmt2->affected_rows != 1);
                        $stmt2->close();
                        return $result;
                    }
                }
            }
        }
        return false;
    }

    /*
     * Changes the profile photo of the user logged in. If problems arise returns false.
     */
    public function changeProfilePhoto($photo) {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "UPDATE users
                      SET profilePhoto = ?
                      WHERE email = ?";
            $stmt = prepareBindExecute($query, "bs", $photo, $email);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Changes the the data of the customer logged in. If problems arise returns false.
     */
    public function changeCustomerData($username, $name, $surname, $birthDate, $birthplace, 
                                       $billingAddress,  $currentAddress = null, $telephone = null) {
        $email = getLoggedUserEmail();
        if ($email !== false || !isCustomer($email)) {
            $query = "UPDATE customers
                      SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?, billingAddress = ?, telephone = ?
                      WHERE email = ?";
            $stmt = prepareBindExecute($query, "sssssssis", $username, $name, 
                                       $surname, $birthDate, $birthplace, $currentAddress, 
                                       $billingAddress, $telephone, $email);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Changes the the data of the customer logged in. If problems arise returns false.
     */
    public function changePromoterData($website) {
        $email = getLoggedUserEmail();
        if ($email !== false || !isPromoter($email)) {
            $query = "UPDATE users
                      SET website = ?
                      WHERE email = ?";
            $stmt = prepareBindExecute($query, "ss", $website, $email);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Returns a short version of the user with the given email.
     * The profile is returned only if:
     *      - it's a promoter's
     *      - it's a customer's, and the request is either from a promoter, an admin or the customer themselves
     *      - it's an admin's, and the request is from that admin
     * Otherwise, false is returned
     */
    public function getUserShortProfile($email) {
        if (isPromoter($email)) {
            return getShortPromoterProfile($email);
        } else if (isCustomer($email)) {
            $loggedEmail = getLoggedUserEmail();
            if ($loggedEmail !== false && (isPromoter($loggedEmail) || isAdmin($loggedEmail) || isUserLoggedIn($loggedEmail))) {
                return getShortCustomerProfile($email);
            }
        } else if (isAdmin($email) && isUserLoggedIn($email)){
            return getAdminProfile($email);
        } else {
            return false;
        }
    }

    /*
     * Returns a long version of the logged in user profile. If problems arise, false is returned.
     */
    public function getLoggedUserLongProfile() {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            if (isPromoter($email)) {
                return getLongPromoterProfile($email);
            } else if (isCustomer($email)) {
                return getShortCustomerProfile($email);
            } else if (isAdmin($email)) {
                return getAdminProfile($email);
            }
        }
        return false;
    }

    /*
     * Deletes the account of the logged user, if the $password is correct. Otherwise, or if problems arise, returns false;
     */
    public function deleteLoggedUser($password) {
        // assuming there will be a cascade delete for customers and promoters tables
        $email = getLoggedUserEmail();
        if ($email !== false && checkLogin($email, $password)) {
            $query = "DELETE FROM users
                      WHERE email = ?";
            $stmt = prepareBindExecute($query, "s", $_SESSION["email"]);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*****************************/
    /***** EVENTS FUNCTIONS *****/
    /***************************/
    /*
     * Returns all the ids of the events with still open seats with a date in the future.
     * If problems arise, returns false.
     */
    public function getEventIds() {
        $query = "SELECT id
                  FROM events 
                  WHERE dateTime >= ?
                  ORDER BY (SELECT COUNT(customerEmail)
                            FROM subscriptions
                            WHERE id = eventId)";
        $stmt = prepareBindExecute($query, "s", date("Y-m-d H:i:s"));
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /*
     * Returns info about the event with the given $eventId.
     * If problems arise, returns false.
     */
    public function getEventInfo($eventId) {
        // TODO: maybe it will be necessary to usa aliases for the fields
        // TODO: price is the correct name?
        $query = "SELECT e.id, e.name, e.place, e.dateTime, e.description, e.site, e.type, e.price, p.organizationName, e.promoterEmail, e.seats - COUNT(*) as freeSeats
                  FROM events e, subscriptions s, promoters p
                  WHERE e.id = ?
                    AND e.id = s.eventId
                    AND e.promoterEmail = p.email
                  GROUP BY e.id";
        $stmt = prepareBindExecute($query, "s", $eventId);
        if ($stmt !== false) {
            return $stmt->fetch();
        }
        return false;
    }

    /*
     * Returns all the possible places for the events.
     * If problems arise, returns false.
     */
    public function getEventsPlaces() {
        $query = "SELECT DISTINCT place
                  FROM events";
        $result = $this->db->query($query); // no risk of SQL injection
        return $result === false ? false : $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the possible types of the events.
     * If problems arise, returns false.
     */
    public function getEventsTypes() {
        $query = "SELECT DISTINCT id, name
                  FROM eventCategories";
        $result = $this->db->query($query); // no risk of SQL injection
        return $result === false ? false : $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the ids of the events in the given $place, on the given $date, 
     * of the given $typeId and with $free or not seats.
     * If problems arise, returns false.
     */
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
        $stmt = prepareBindExecute($query, "s", $place);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /*
     * Inserts a new event in the database, by the promoter currently logged in.
     * If problems arise, or if the logged user is not a promoter, returns false, 
     * otherwise returns the id of the new event.
     */
    public function createEvent($name, $place, $dateTime, $seats, $description, $type, $price, $site = null) {
        $email = getLoggedUserEmail();
        if ($email !== false && isPromoter($email) { // only promoters can add events
            // TODO: price is the correct name?
            $query = "INSERT INTO events(name, place, dateTime, seats, description, site, type, price, promoterEmail)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = prepareBindExecute($query, "sssisss", $name, $place, $dateTime, $seats, $description, $site, $type, $price, $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows != 1 ? false : $stmt->insert_id;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /* 
     * Returns the emails of the subscribers to a certain event. If problems arise, returns false.
     */
    public function getSubscribers($eventId) {
        $query = "SELECT customerEmail as email
                  FROM subscriptions
                  WHERE eventId = ?";
        $stmt = prepareBindExecute($query, "i", $eventId);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /* 
     * Returns the emails of the subscribers to a certain event. If problems arise, returns false.
     */
    public function getSubscribedEvents() {
        // TODO: maybe it will be necessary to usa aliases for the fields
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "SELECT e.id, e.name, e.place, e.dateTime, e.description, e.site, p.organizationName
                      FROM events e, subscriptions s
                      WHERE s.customerEmail = ?
                        AND e.id = s.eventId";
            $stmt = prepareBindExecute($query, "s", $email);
            if ($stmt !== false) {
                $result = $stmt->get_result(); // TODO: could merge these two lines?
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        return false;
    }

    /*
     * Changes the date of the given event, sending to the users the given message, if the 
     * user logged in is the owner of the event.
     * If problems arise, or the logged user is not the owner of the event, returns false.
     * Otherwise, returns true;
     */
    public function changeEventDate($eventId, $newDate, $notificationMessage) {
        if (isLoggedUserEventOwner($eventId)) {
            $query = "UPDATE events
                      SET date = ?
                      WHERE id = ?";
            $stmt = prepareBindExecute($query, "si", $newDate, $eventId);
            if ($stmt !== false) {
                $result = $stmt->affected_rows == 1
                        ? sendNotificationToEventSubscribers($eventId, $notificationMessage)
                        : false;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /***************************/
    /***** CART FUNCTIONS *****/
    /*************************/
    /*
     * Insert a ticket into the logged user's cart, if such user is a customer.
     * If problems arise, or if the logged user isn't a customer, returns false.
     * Otherwise, returns true.
     */
    public function putTicketIntoCart($eventId) {
        $email = getLoggedUserEmail();
        if ($email !== false && isCustomer($email)) {
            $query = "INSERT INTO carts(eventId, customerEmail)
                      SELECT ?, ?
                      FROM events
                      WHERE id IN (SELECT e.id 
                                   FROM events e 
                                   WHERE seats > (SELECT COUNT(customerEmail) 
                                                  FROM subscriptions 
                                                  WHERE eventId = e.id))";
            $stmt = prepareBindExecute($query, "is", $eventId, $email);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Remove a ticket from the logged user's cart.
     * If problems arise, returns false. Otherwise, returns true.
     */
    public function removeTicketFromCart($eventId) {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "DELETE FROM carts
                      WHERE customerEmail = ?
                        AND eventId = ?";
            $stmt = prepareBindExecute($query, "si", $email, $eventId);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != 1); // TODO: if not ticket is found, what do we return?
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Buys a ticket, and eventually removes it from the logged user's cart.
     * If problems arise, returns false. Otherwise, returns true.
     */
    public function buyEventTicket($eventId) {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "INSERT INTO subscriptions(eventId, customerEmail)
                      SELECT ?, ?
                      FROM events
                      WHERE id IN (SELECT e.id 
                                   FROM events e 
                                   WHERE seats > (SELECT COUNT(customerEmail) 
                                                  FROM subscriptions 
                                                  WHERE eventId = e.id))";
            $stmt = prepareBindExecute($query, "is", $eventId, $email);
            if ($stmt !== false) {
                if ($stmt->affected_rows != 1) {
                    $result = false;
                } else {
                    $result = true;
                    removeTicketFromCart($eventId);
                }
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /************************************/
    /***** NOTIFICATIONS FUNCTIONS *****/
    /**********************************/
    /* 
     * Insert a new type of notification into the database.
     * Returns false if problems arise.
     */
    private function insertNewNotification($message) {
        $query = "INSERT INTO notifications(message)
                  VALUES (?)";
        $stmt = prepareBindExecute($query, "s", $message);
        if ($stmt !== false) {
            $result = $stmt->affected_rows != 1 ? null : $stmt->insert_id;
            $stmt->close();
            return $result;
        }
        return false;
    }

    /*
     * Send a notification with the given $message to the subscribers of the event with the given $eventId.
     * If problems arise, returns false.
     */
    private function sendNotificationToEventSubscribers($eventId, $message) { // TODO: should be public?
        $notificationId = insertNewNotification($message);
        if ($notificationId != null) { // TODO: check typing
            $query = "INSERT INTO usersNotifications(email, dateTime, notificationId, visualized)
                      SELECT customerEmail, ?, ?, false
                      FROM subscriptions
                      WHERE eventId = ?";
            $stmt = prepareBindExecute($query, "ssi", date("Y-m-d H:i:s"), $notificationId, $eventId);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != -1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Returns the notification sent to the currently logged user.
     * If problems arise, returns false.
     */ 
    public function getLoggedUserNotifications() {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "SELECT notificationId, dateTime, visualized, message
                      FROM usersNotifications, notifications
                      WHERE notificationId = id
                        AND email = ?";
            $stmt = prepareBindExecute($query, "s", $email);
            if ($stmt !== false) {
                $result = $stmt->get_result(); // TODO: could merge these two lines?
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        return false;
    }

    /*
     * Delete a notification of the logged user, sent on $dateTime and of type $notificationId.
     * If problems arise, returns false.
     */
    public function deleteUserNotification($notificationId, $dateTime) {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "DELETE FROM usersNotifications
                      WHERE notificationId = ? AND email = ? AND dateTime = ?";
            $stmt = prepareBindExecute($query, "iss", $notificationId, $email, $dateTime);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != -1);
                $stmt->close();
                deleteNotificationTypesIfNotUsedAnymore();
                return $result;
            }
        }
        return false;
    }

    /*
     * Change the state (visualized or not) of the notification of the currently logged user
     * sent on $dateTime and with the given type $notificationId.
     * If problems arise, returns false.
     */
    public function toggleNotificationView($notificationId, $dateTime) {
        $email = getLoggedUserEmail();
        if ($email !== false) {
            $query = "UPDATE usersNotifications
                      SET visualized = not visualized
                      WHERE email = ?
                        AND notificationId = ?
                        AND dateTime = ?";
            $stmt = prepareBindExecute($query, "sis", $email, $notificationId, $dateTime);
            if ($stmt !== false) {
                $result = ($stmt->affected_rows != -1);
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Deletes the notification types that are not used anymore.
     */
    private function deleteNotificationTypesIfNotUsedAnymore() {
        $query = "DELETE FROM notifications
                  WHERE id NOT IN (SELECT notificationId
                                   FROM usersNotifications)";
        $result = $this->db->query($query); // no risk of SQL injection
        return $result;
    }

    /**********************/
    /***** UTILITIES *****/
    /********************/
    private function getShortCustomerProfile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto
                  FROM users u, customers c 
                  WHERE u.email = ? 
                      AND u.email = c.email";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    private function getLongCustomerProfile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress, telephone, email
                  FROM users u, customers c 
                  WHERE u.email = ? 
                      AND u.email = c.email";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    private function getShortPromoterProfile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? 
                      AND u.email = p.email";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    private function getLongPromoterProfile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoter p 
                  WHERE u.email = ? 
                      AND u.email = p.email";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    private function getAdminProfile($email) {
        $query = "SELECT email, profilePhoto
                  FROM users u
                  WHERE u.email = ?";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result(); // TODO: could merge these two lines?
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }

    private function hashPassword($password, $salt) {
        return hash('sha512', $password.$salt);
    }

    private function isUserType($email, $type) {
        $query = "SELECT *
                  FROM users
                  WHERE email = ?
                    AND type = ?";
        $stmt = prepareBindExecute($query, "si", $email, $type);
        return ($stmt !== false && $stmt->fetch() != null);
    }

    private function isPromoter($email) {
        // TODO: change to use type
        /* return isUserType($email, TYPE); */
        $query = "SELECT *
                  FROM promoters
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt = prepareBindExecute($query, "s", $email);
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
        $stmt = prepareBindExecute($query, "s", $email);
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
        $stmt = prepareBindExecute($query, "s", $email);
        $stmt->execute();
        return ($stmt->fetch() != null); // if there is no promoter with the given email returns false
    }
    
    private function isUserLoggedIn($email) {
        return $_SESSION["email"] == $email;
    }

    private function isLoggedUserEventOwner($eventId) {
        $eventInfo = getEventInfo($eventId);
        return isUserLoggedIn($eventInfo["promoterEmail"]);
    }
    
    /*
     * Prepares a statement from the given query, and binds the arguments to the statement using the given binding string.
     * Then, the statement is executed and returned for use.
     * If something fails, false is returned.
     */
    private function prepareBindExecute($query, $bindings, ...$arguments) {
        $stmt = $this->db->prepare($query);
        if ($stmt !== false) {
            $stmt = prepareBindExecute($query, $bindings, ...$arguments);
            $stmt->execute();
        }
        return $stmt;
    }

    /*
     * Returns the email of the currently logged user, false if no user is logged
     */
    private function getLoggedUserEmail() {
        return isset($_SESSION["email"]) ? $_SESSION["email"] : false;
    }
}
?>