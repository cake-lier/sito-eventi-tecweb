<?php

class DatabaseHelper{
    define("CUSTOMER_TYPE_CODE", "c");
    define("PROMOTER_TYPE_CODE", "p");
    define("ADMIN_TYPE_CODE", "a");

    private $db;

    public function __construct($server_name, $username, $password, $db_name){
        $this->db = new mysqli($server_name, $username, $password, $db_name);
        if($this->db->connect_error) {
            die("Failed to connect to the database, error: " . $this->db->connect_error);
        }
    }

    /****************************/
    /***** USERS FUNCTIONS ******/
    /****************************/
    /*
     * Inserts a new user into the database, returns true if everything went well, false otherwise.
     */
    private function insert_user($email, $password, $profile_photo, $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = prepare_bind_execute($query, "ssbs", $email, password_hash($password, PASSWORD_DEFAULT), $profile_photo,
                                     $type);
        if ($stmt !== false) {
            $result = $stmt->affected_rows === 1;
            $stmt->close();
            return $result;
        }
        return false;
    }

    /*
     * Inserts a new customer into the database, returns true if everything went well, false otherwise.
     */
    public function insert_customer($email, $password, $profile_photo, $billing_address, $birth_date, $birthplace,
                                    $name, $surname, $username, $telephone = null, $current_address = null) {
        if (insert_user($email, $password, $profile_photo, CUSTOMER_TYPE_CODE)) {
            $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username,
                                  telephone, currentAddress)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = prepare_bind_execute($query, "sssssssis", $email, $billing_address, $birth_date, $birthplace, $name,
                                         $surname, $username, $telephone, $current_address);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Inserts a new promoter into the database, returns true if everything went well, false otherwise.
     */
    public function insert_promoter($email, $password, $profile_photo, $organization_name, $VAT_id, $website = null) {
        if (insert_user($email, $password, $profile_photo, PROMOTER_TYPE_CODE)) {
            $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                      VALUES (?, ?, ?, ?)";
            $stmt = prepare_bind_execute($query, "ssis", $email, $organization_name, $VAT_id, $website);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
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
    public function check_login($email, $plain_password) {
        $query = "SELECT email, password
                  FROM users
                  WHERE email = ?";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt !== false) {
            $user_email = "";
            $db_password = "";
            // Recupera il risultato della query e lo memorizza nelle relative variabili
            $stmt->bind_result($user_email, $db_password);
            $stmt->fetch();
            $result = ($stmt->num_rows === 1) && password_verify($plain_password, $db_password);
            $stmt->close();
            return $result;
        }
        return false;
    }
    
    /*
     * Changes the password of the account with the given $email, only if the $old_password is correct and
     * the user is logged in. If problems arise or if the above conditions are not respected, returns false.
     */
    public function change_password($email, $old_password, $new_password) {
        if (check_login($email, $old_password) && is_user_logged_in($email)) {
            $query = "UPDATE users
                      SET password = ?
                      WHERE email = ?";
            $stmt = prepare_bind_execute($query, "ss", password_hash($new_password, PASSWORD_DEFAULT), $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Changes the profile photo of the user logged in. If problems arise returns false.
     */
    public function change_profile_photo($photo) {
        $email = get_logged_user_email();
        if ($email !== false) {
            $query = "UPDATE users
                      SET profilePhoto = ?
                      WHERE email = ?";
            $stmt = prepare_bind_execute($query, "bs", $photo, $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Changes the data of the customer logged in. If problems arise returns false.
     */
    public function change_customer_data($username, $name, $surname, $birth_date, $birthplace, $billing_address,
                                         $current_address = null, $telephone = null) {
        $email = get_logged_user_email();
        if ($email !== false && is_customer($email)) {
            $query = "UPDATE customers
                      SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?,
                          billingAddress = ?, telephone = ?
                      WHERE email = ?";
            $stmt = prepare_bind_execute($query, "sssssssis", $username, $name, $surname, $birth_date, $birthplace,
                                         $current_address, $billing_address, $telephone, $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*
     * Changes the the data of the customer logged in. If problems arise returns false.
     */
    public function change_promoter_data($website) {
        $email = get_logged_user_email();
        if ($email !== false && is_promoter($email)) {
            $query = "UPDATE users
                      SET website = ?
                      WHERE email = ?";
            $stmt = prepare_bind_execute($query, "ss", $website, $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
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
    public function get_user_short_profile($email) {
        if (is_promoter($email)) {
            return get_short_promoter_profile($email);
        } else if (is_customer($email)) {
            $logged_email = get_logged_user_email();
            if ($logged_email !== false
                && (is_promoter($loggedEmail) || is_admin($loggedEmail) || is_user_logged_in($loggedEmail))) {
                return get_short_customer_profile($email);
            }
        } else if (is_admin($email) && is_user_logged_in($email)){
            return get_admin_profile($email);
        }
        return false;
    }

    /*
     * Returns a long version of the logged in user profile. If problems arise, false is returned.
     */
    public function get_logged_user_long_profile() {
        $email = get_logged_user_email();
        if ($email !== false) {
            if (is_promoter($email)) {
                return get_long_promoter_profile($email);
            } else if (is_customer($email)) {
                return get_long_customer_profile($email);
            } else if (is_admin($email)) {
                return get_admin_profile($email);
            }
        }
        return false;
    }

    /*
     * Deletes the account of the logged user, if the $password is correct. Otherwise, or if problems arise, returns
     * false.
     */
    public function delete_logged_user($password) {
        // assuming there will be a cascade delete for customers and promoters tables
        $email = get_logged_user_email();
        if ($email !== false && check_login($email, $password)) {
            $query = "DELETE FROM users
                      WHERE email = ?";
            $stmt = prepare_bind_execute($query, "s", $email);
            if ($stmt !== false) {
                $result = $stmt->affected_rows === 1;
                $stmt->close();
                return $result;
            }
        }
        return false;
    }

    /*****************************/
    /***** EVENTS FUNCTIONS ******/
    /*****************************/
    /*
     * Returns all the ids of the events with still open seats with a date in the future.
     * If problems arise, returns false.
     */
    public function get_event_ids() {
        $query = "SELECT id
                  FROM events 
                  WHERE dateTime >= ?";
        $stmt = prepare_bind_execute($query, "s", date("Y-m-d H:i:s"));
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /*
     * Returns info about the event with the given $event_id.
     * If problems arise, returns false.
     */
    public function get_event_info($event_id) {
        $query = "SELECT e.name AS name, e.place AS place, e.dateTime AS dateTime, e.description AS description,
                         e.site AS site, p.organizationName AS organizationName, e.promoterEmail AS promoterEmail
                  FROM events e, promoters p
                  WHERE e.id = ? AND e.promoterEmail = p.email";
        $stmt = prepare_bind_execute($query, "i", $event_id);
        if (!$stmt) {
            return false;
        }
        $event = $stmt->get_result()->fetch();
        $tickets_query = "SELECT sc.name AS name, sc.price AS price, COUNT(*) - COUNT(t.customerEmailPurchase)
                                - COUNT(t.customerEmailChoice) AS freeSeats
                          FROM events e, seatCategories sc, tickets t
                          WHERE e.id = ? AND e.id = sc.eventId AND t.seatId = sc.id AND t.eventId = e.id
                          GROUP BY sc.name, sc.price";
        $tickets_stmt = prepare_bind_execute($tickets_query, "i", $event_id);
        if (!$tickets_stmt) {
            return false;
        }
        $event["seatCategories"] = $tickets_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $cat_query = "SELECT ec.name AS name
                      FROM events e, eventCategories ec, eventsToCategories etc
                      WHERE e.id = ? AND e.id = etc.eventId AND etc.categoryId = ec.id";
        $cat_stmt = prepare_bind_execute($cat_query, "i", $event_id);
        if (!$cat_stmt) {
            return false;
        }
        $event["categories"] = $cat_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $event;
    }

    /*
     * Returns all the possible places for the events.
     * If problems arise, returns false.
     */
    public function get_events_places() {
        $query = "SELECT DISTINCT place
                  FROM events";
        $result = $this->db->query($query); // no risk of SQL injection
        return $result === false ? false : $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the possible types of the events.
     * If problems arise, returns false.
     */
    public function get_events_types() {
        $query = "SELECT DISTINCT name
                  FROM eventCategories";
        $result = $this->db->query($query); // no risk of SQL injection
        return $result === false ? false : $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the ids of the events in the given $place, on the given $date, of the given $typeId and with $free or
     * not seats.
     * If problems arise, returns false.
     */
    //TODO: How to properly filter events? Maybe more functions?
    public function getEventIdsFiltered($place, $date, $typeId, $free = true) {
        $condition = "";
        $bindings = "";
        if ($place !== null) {
            $condition = "place = ?";
            $bindings = "s";
        }
        if ($date !== null) {
            $condition = $condition == "" ? "" : $condition." AND ";
            $condition = $condition."date = ?";
            $bindings = $bindings."s";
        }
        if ($type !== null) {
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
                  WHERE " . $condition;
        $stmt = prepareBindExecute($query, $bindings, NULL); //TODO: Correct this function call
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
     * Returns the emails of the buyers of a certain event. If problems arise, returns false.
     */
    public function get_buyers($event_id) {
        $query = "SELECT customerEmail as email
                  FROM subscriptions
                  WHERE eventId = ?";
        $stmt = prepareBindExecute($query, "i", $eventId);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    /***** UTILITIES ******/
    /**********************/

    /*
     * Returns the short version of a customer profile, or false if an error occured.
     */
    private function get_short_customer_profile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } 
        return false;
    }

    /*
     * Returns the long version of a customer profile, or false if an error occured.
     */
    private function get_long_customer_profile($email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress,
                         telephone, email
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } 
        return false;
    }

    /*
     * Returns the short version of a promoter profile, or false if an error occured.
     */
    private function get_short_promoter_profile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } 
        return false;
    }

    /*
     * Returns the long version of a customer profile, or false if an error occured.
     */
    private function get_long_promoter_profile($email) {
        $query = "SELECT email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoter p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /*
     * Returns the data inherent to an admin profile, or false if an error occured.
     */
    private function get_admin_profile($email) {
        $query = "SELECT email, profilePhoto
                  FROM users u
                  WHERE u.email = ?";
        $stmt = prepareBindExecute($query, "s", $email);
        if ($stmt !== false) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } 
        return false;
    }

    /*
     * Checks if the given $email is associated to an user of the given $type. It returns false also in case of error.
     */
    private function is_user_type($email, $type) {
        $query = "SELECT *
                  FROM users
                  WHERE email = ? AND type = ?";
        $stmt = prepare_bind_execute($query, "ss", $email, $type);
        return ($stmt !== false && $stmt->fetch() != null);
    }

    /*
     * Checks if the given $email is associated to a promoter user. It returns false also in case of error.
     */
    private function is_promoter($email) {
        return is_user_type($email, PROMOTER_TYPE_CODE);
    }

    /*
     * Checks if the given $email is associated to a customer user. It returns false also in case of error.
     */
    private function is_customer($email) {
        return is_user_type($email, CUSTOMER_TYPE_CODE);
    }

    /*
     * Checks if the given $email is associated to an admin user. It returns false also in case of error.
     */
    private function is_admin($email) {
        return is_user_type($email, ADMIN_TYPE_CODE);
    }
    
    /*
     * Checks if the user on which the current operation is being done is the user that is currently logged in.
     */
    private function is_user_logged_in($email) {
        return $_SESSION["email"] === $email;
    }

    private function isLoggedUserEventOwner($eventId) {
        $eventInfo = getEventInfo($eventId);
        return isUserLoggedIn($eventInfo["promoterEmail"]);
    }
    
    /*
     * Prepares a statement from the given query, and binds the arguments to the statement using the given binding
     * string. Then, the statement is executed and returned for use.
     * If something fails, false is returned.
     */
    private function prepare_bind_execute($query, $bindings, ...$arguments) {
        $stmt = $this->db->prepare($query);
        if ($stmt !== false) {
            $stmt = bind_param($query, $bindings, ...$arguments);
            $stmt->execute();
        }
        return $stmt;
    }

    /*
     * Returns the email of the currently logged user, false if no user is logged.
     */
    private function get_logged_user_email() {
        return isset($_SESSION["email"]) ? $_SESSION["email"] : false;
    }
}
?>
