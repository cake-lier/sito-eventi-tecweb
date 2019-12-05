<?php
declare(strict_types = 1);

class DatabaseHelper{
    define("CUSTOMER_TYPE_CODE", "c");
    define("PROMOTER_TYPE_CODE", "p");
    define("ADMIN_TYPE_CODE", "a");
    define("QUERY_ERROR", "An error occured while executing the query");
    define("PRIVILEGE_ERROR", "The user performing the operation hasn't enough privileges to do so");
    define("DATE_ERROR", "The date should be a future date from now");

    private $db;

    public function __construct(string $server_name, string $username, string $password, string $db_name){
        $this->db = new mysqli($server_name, $username, $password, $db_name);
        if($this->db->connect_error) {
            die("Failed to connect to the database, error: " . $this->db->connect_error);
        }
    }

    /****************************/
    /***** USERS FUNCTIONS ******/
    /****************************/
    /*
     * Inserts a new customer into the database. Throws an exception if something went wrong.
     */
    public function insert_customer(string $email, string $password, string $profile_photo, string $billing_address,
                                    string $birth_date, string $birthplace, string $name, string $surname,
                                    string $username, string $telephone = null, string $current_address = null) {
        if (!$this->insert_user($email, $password, $profile_photo, CUSTOMER_TYPE_CODE)) {
            throw new Exception(QUERY_ERROR);
        }
        $query = "INSERT INTO customers(email, billingAddress, birthDate, birthplace, name, surname, username,
                              telephone, currentAddress)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare_bind_execute($query, "sssssssss", $email, $billing_address, $birth_date, $birthplace,
                                            $name, $surname, $username, $telephone, $current_address);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
    }

    /*
     * Inserts a new promoter into the database. Throws an exception if something went wrong.
     */
    public function insert_promoter(string $email, string $password, $profile_photo, string $organization_name,
                                    string $VAT_id, string $website = null) {
        if (!$this->insert_user($email, $password, $profile_photo, PROMOTER_TYPE_CODE)) {
            throw new Exception(QUERY_ERROR);
        }
        $query = "INSERT INTO promoters(email, organizationName, VATid, website)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepare_bind_execute($query, "ssss", $email, $organization_name, $VAT_id, $website);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
    }

    /*
     * Given the $email and the $plain_password, checks if there is an account bound to them. If $email or
     * $plain_password are wrong, it returns false, otherwise true. Throws an exception if something went wrong.
     */
    public function check_login(string $email, string $plain_password) {
        $query = "SELECT email, password
                  FROM users
                  WHERE email = ?";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->num_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
        $user_email = "";
        $db_password = "";
        // Gets the query result and saves it into the corresponding variables
        $stmt->bind_result($user_email, $db_password);
        $stmt->fetch();
        $stmt->close();
        return password_verify($plain_password, $db_password);
    }
    
    /*
     * Changes the password of the account with the given $email, only if the $old_password is correct and
     * the user is logged in. If the above conditions are not respected, returns false. Throws an exception if something
     * went wrong.
     */
    public function change_password(string $email, string $old_password, string $new_password) {
        try {
            if ($this->check_login($email, $old_password) && $this->is_user_logged_in($email)) {
                $query = "UPDATE users
                          SET password = ?
                          WHERE email = ?";
                $stmt = $this->prepare_bind_execute($query, "ss", password_hash($new_password, PASSWORD_DEFAULT),
                                                    $email);
                if ($stmt === false) {
                    throw new Exception(QUERY_ERROR);
                } else if ($stmt->affected_rows !== 1) {
                    $stmt->close();
                    throw new Exception(QUERY_ERROR);
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
    public function change_profile_photo($photo) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "UPDATE users
                  SET profilePhoto = ?
                  WHERE email = ?";
        $stmt = $this->prepare_bind_execute($query, "bs", $photo, $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
    }

    /*
     * Changes the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function change_customer_data(string $username, string $name, string $surname, string $birth_date,
                                         string $birthplace, string $billing_address, string $current_address = null,
                                         string $telephone = null) {
        $email = $this->get_logged_user_email();
        if ($email === false || !$this->is_customer($email)) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "UPDATE customers
                  SET username = ?, name = ?, surname = ?, birthDate = ?, birthplace = ?, currentAddress = ?,
                      billingAddress = ?, telephone = ?
                  WHERE email = ?";
        $stmt = $this->prepare_bind_execute($query, "sssssssss", $username, $name, $surname, $birth_date, $birthplace,
                                            $current_address, $billing_address, $telephone, $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
    }

    /*
     * Changes the the data of the customer logged in. Throws an exception if something went wrong.
     */
    public function change_promoter_data(string $website) {
        $email = $this->get_logged_user_email();
        if ($email === false || !$this->is_promoter($email)) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "UPDATE users
                  SET website = ?
                  WHERE email = ?";
        $stmt = $this->prepare_bind_execute($query, "ss", $website, $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
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
    public function get_user_short_profile(string $email) {
        if ($this->is_promoter($email)) {
            return get_short_promoter_profile($email);
        } else if ($this->is_customer($email)) {
            $logged_email = $this->get_logged_user_email();
            if ($logged_email !== false && ($this->is_promoter($loggedEmail)
                                            || $this->is_admin($loggedEmail)
                                            || $this->is_user_logged_in($loggedEmail))) {
                return $this->get_short_customer_profile($email);
            }
        } else if ($this->is_admin($email) && $this->is_user_logged_in($email)){
            return $this->get_admin_profile($email);
        }
        throw new Exception(PRIVILEGE_ERROR);
    }

    /*
     * Returns a long version of the logged in user profile. Throws an exception if something went wrong.
     */
    public function get_logged_user_long_profile() {
        $email = $this->get_logged_user_email();
        if (!$email) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        if ($this->is_promoter($email)) {
            return $this->get_long_promoter_profile($email);
        } else if ($this->is_customer($email)) {
            return $this->get_long_customer_profile($email);
        } else if ($this->is_admin($email)) {
            return $this->get_admin_profile($email);
        }
        throw new Exception(PRIVILEGE_ERROR);
    }

    /*
     * Deletes the account of the logged user, if the $password is correct, and returns true. Otherwise, it returns
     * false. Throws an exception if something went wrong.
     */
    public function delete_logged_user(string $password) {
        // Assuming there will be a cascade delete for customers and promoters tables
        $email = $this->get_logged_user_email();
        if (!$email) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        try {
            if ($this->check_login($email, $password)) {
                $query = "DELETE FROM users
                          WHERE email = ?";
                $stmt = prepare_bind_execute($query, "s", $email);
                if ($stmt === false) {
                    throw new Exception(QUERY_ERROR);
                } else if ($stmt->affected_rows !== 1) {
                    $stmt->close();
                    throw new Exception(QUERY_ERROR);
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
    private function insert_user(string $email, string $password, $profile_photo, $type) {
        $query = "INSERT INTO users(email, password, profilePhoto, type)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepare_bind_execute($query, "ssbs", $email, password_hash($password, PASSWORD_DEFAULT),
                                            $profile_photo, $type);
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
    private function get_short_customer_profile(string $email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
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
    private function get_long_customer_profile(string $email) {
        $query = "SELECT username, name, surname, birthDate, birthplace, profilePhoto, currentAddress, billingAddress,
                         telephone, email
                  FROM users u, customers c 
                  WHERE u.email = ? AND u.email = c.email";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
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
    private function get_short_promoter_profile(string $email) {
        $query = "SELECT email, profilePhoto, organizationName, website
                  FROM users u, promoters p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
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
    private function get_long_promoter_profile(string $email) {
        $query = "SELECT email, profilePhoto, organizationName, website, VATid
                  FROM users u, promoter p 
                  WHERE u.email = ? AND u.email = p.email";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
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
    private function get_admin_profile(string $email) {
        $query = "SELECT email, profilePhoto
                  FROM users u
                  WHERE u.email = ?";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt !== false) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } 
        return false;
    }

    /*****************************/
    /***** EVENTS FUNCTIONS ******/
    /*****************************/
    /*
     * Returns all the ids of the events with a date in the future. Throws an exception if something went wrong.
     */
    public function get_event_ids() {
        $query = "SELECT id
                  FROM events
                  WHERE dateTime >= ?";
        $stmt = $this->prepare_bind_execute($query, "s", date("Y-m-d H:i:s"));
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $free_seats = $this->get_events_free_seats(array_column($result, "id"));
        if ($free_seats === false) {
            throw new Exception(QUERY_ERROR);
        }
        array_walk($result, function($e, $i) {
            $e["freeSeats"] = $free_seats[$i];
        });
        usort($result, function($fst, $snd) {
            return $snd["freeSeats"] - $fst["freeSeats"];
        });
        return array_column($result, "id");
    }

    /*
     * Returns info about the event with the given $event_id. Throws an exception if something went wrong.
     */
    public function get_event_info(int $event_id) {
        $query = "SELECT e.name AS name, e.place AS place, e.dateTime AS dateTime, e.description AS description,
                         e.site AS site, p.organizationName AS organizationName, e.promoterEmail AS promoterEmail,
                         SUM(s.seats) as totalSeats
                  FROM events e, promoters p, seatCategories s
                  WHERE e.id = ? AND e.promoterEmail = p.email AND s.eventId = e.id
                  GROUP BY e.name, e.place, e.dateTime, e.description, e.site, p.organizationName, e.promoterEmail";
        $stmt = $this->prepare_bind_execute($query, "i", $event_id);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $event = $stmt->get_result()->fetch();
        $free_seats = $this->get_events_free_seats($event_id);
        if ($free_seats === false) {
            throw new Exception(QUERY_ERROR);
        }
        $event["freeSeats"] = $free_seats[0];
        $seats_query = "SELECT sc.name AS name, sc.price AS price, sc.seats AS seats, SUM(p.amount) 
                        + SUM(c.amount) AS occupiedSeats
                        FROM events e, seatCategories s, purchases p, carts c
                        WHERE e.id = ? AND e.id = s.eventId AND p.seatId = s.id AND p.eventId = s.eventId
                              AND c.seatId = s.id AND c.eventId = s.eventId
                        GROUP BY sc.name, sc.price, sc.seats";
        $seats_stmt = $this->prepare_bind_execute($tickets_query, "i", $event_id);
        if ($seats_stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $event["seatCategories"] = $tickets_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $cat_query = "SELECT ec.name AS name
                      FROM events e, eventCategories ec, eventsToCategories etc
                      WHERE e.id = ? AND e.id = etc.eventId AND etc.categoryId = ec.id";
        $cat_stmt = $this->prepare_bind_execute($cat_query, "i", $event_id);
        if ($cat_stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $event["categories"] = $cat_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $event;
    }

    /*
     * Returns all the possible places for the events. Throws an exception if something went wrong.
     */
    public function get_events_places() {
        $query = "SELECT DISTINCT place
                  FROM events";
        $result = $this->db->query($query); // no risk of SQL injection
        if ($result === false) {
            throw new Exception(QUERY_ERROR);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the possible types of the events. Throws an exception if something went wrong.
     */
    public function get_events_types() {
        $query = "SELECT DISTINCT name
                  FROM eventCategories";
        $result = $this->db->query($query); // no risk of SQL injection
        if ($result === false) {
            throw new Exception(QUERY_ERROR);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Returns all the ids of the events in the given $place, on the given $date, of the given $typeId and with $free or
     * not seats. Throws an exception if something went wrong.
     */
    public function get_event_ids_filtered($place = null, $date = null, $free = true) {
        $condition = "";
        $bindings = "";
        $parameters = array();
        if ($place !== null) {
            $condition = "place = ?";
            $bindings = "s";
            $parameters[] = $place;
        }
        if ($date !== null) {
            $condition = $condition === "" ? "" : $condition . " AND ";
            $condition .= "date = ?";
            $bindings = $bindings . "s";
            $parameters[] = $date;
        }
        if ($free) {
            $condition = $condition == "" ? "" : $condition . " AND ";
            $condition .= "e.id = s.eventId
                           GROUP BY e1.id
                           HAVING SUM(s.seats) > (SELECT COUNT(p.customerEmail) + COUNT(c.customerEmail)
                                                  FROM events e1, seatCategories s1, purchases p, carts c
                                                  WHERE e1.id = e.id AND p.seatId = s1.id AND p.eventId = s1.eventId 
                                                        AND c.seatId = s1.id AND c.eventId = s1.eventId
                                                        AND s1.eventId = e1.id)";
        }
        $query = "SELECT id
                  FROM events e, seatCategories s
                  WHERE " . $condition;
        $stmt = $this->prepare_bind_execute($query, $bindings, $parameters);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Inserts a new event in the database, by the promoter currently logged in. If problems arise, or if the logged
     * user is not a promoter, throws an exception. It returns the id of the new event.
     */
    public function create_event(string $name, string $place, string $date_time, string $description,
                                 string $promoter_email, array $seat_categories, array $event_categories,
                                 string $site = null) {
        $email = $this->get_logged_user_email();
        // Only promoters can add events
        if ($email === false || !is_promoter($email)) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "INSERT INTO events(name, place, dateTime, description, site, promoterEmail)
                  VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $event_date = new Date($date_time);
            if ($event_date <= new Date()) {
                throw new Exception(DATE_ERROR);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $stmt = prepare_bind_execute($query, "ssssss", $name, $place, $date_time, $description, $site, $promoter_email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
        $event_id = $stmt->insert_id;
        $stmt->close();
        $seat_query = "INSERT INTO seatCategories(eventId, name, seats, price)
                       VALUES (?, ?, ?, ?)";
        foreach ($seat_categories as $seat_category) {
            $seat_stmt = prepare_bind_execute($seat_query, "isii", $event_id, $seat_category["name"],
                                              $seat_category["seats"], $seat_category["price"]);
            if ($seat_stmt === false) {
                throw new Exception(QUERY_ERROR);
            } else if ($seat_stmt->affected_rows !== 1) {
                $seat_stmt->close();
                throw new Exception(QUERY_ERROR);
            }
            $seat_stmt->close();
        }
        $categories_query = "INSERT INTO eventCategories(name)
                             SELECT * FROM (SELECT ?) AS tmp
                             WHERE NOT EXISTS (
                                SELECT name FROM eventCategories WHERE name = ?
                             ) LIMIT 1";
        $category_id_query = "SELECT id
                              FROM eventCategories
                              WHERE name = ?
                              LIMIT 1";
        $categories_events_query = "INSERT INTO eventsToCategories(eventId, categoryId)
                                    VALUES (?, ?)";
        foreach ($event_categories as $event_category) {
            $categories_stmt = $this->prepare_bind_execute($categories_query, "ss", $event_category, $event_category);
            if ($categories_stmt === false) {
                throw new Exception(QUERY_ERROR);
            } 
            $categories_stmt->close();
            $category_id = -1;
            switch ($categories_stmt->affected_rows) {
                case 1:
                    $category_id = $categories_stmt->insert_id;
                    break;
                case 0:
                    $category_id_stmt = $this->prepare_bind_execute($category_id_query, "s", $event_category);
                    if ($category_id_stmt === false) {
                        throw new Exception(QUERY_ERROR);
                    }
                    $category_id_stmt->bind_result($category_id);
                    $category_id_stmt->fetch();
                    if ($category_id === -1) {
                        throw new Exception(QUERY_ERROR);
                    }
                default:
                    throw new Exception(QUERY_ERROR);
                    break;
            }
            $categories_events_stmt = $this->prepare_bind_execute($categories_events_query, "ii", $event_id, 
                                                                  $category_id);
            if ($categories_events_stmt === false) {
                throw new Exception(QUERY_ERROR);
            } else if ($categories_events_stmt->affected_rows !== 1) {
                $categories_events_stmt->close();
                throw new Exception(QUERY_ERROR);
            }
        }
        return $event_id;
    }

    /* 
     * Returns the emails of the buyers of a certain event. Throws an exception if something went wrong.
     */
    public function get_buyers(int $event_id) {
        $query = "SELECT DISTINCT customerEmail as email
                  FROM purchases
                  WHERE eventId = ?";
        $stmt = prepareBindExecute($query, "i", $eventId);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* 
     * Returns the events the logged user has bought. Throws an exception if something went wrong.
     */
    public function get_purchased_events() {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "SELECT DISTINCT e.name AS name, e.place AS place, e.dateTime AS dateTime,
                         e.description AS description, e.site AS site, p.organizationName AS organizationName
                  FROM events e, purchases p
                  WHERE p.customerEmail = ? AND e.id = p.eventId";
        $stmt = $this->prepare_bind_execute($query, "s", $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Changes the date of the given event, sending to the users the given message, if the user logged in is the owner
     * of the event. If problems arise, or the logged user is not the owner of the event, throws an exception.
     */
    public function change_event_date(int $event_id, string $new_date, string $notification_message) {
        if (!$this->is_logged_user_event_owner($event_id)) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "UPDATE events
                  SET dateTime = ?
                  WHERE id = ?";
        $stmt = $this->prepare_bind_execute($query, "si", $new_date, $event_id);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
        $stmt->close();
        $this->send_notification_to_event_purchasers($event_id, $notification_message)
    }

    /*
     * Gets the remaining free seats for every event which event id was passed as a parameter.
     */
    private function get_events_free_seats(...int $event_ids) {
        $query_total_seats = "SELECT SUM(s.seats) as totalSeats
                              FROM events e, seatCategories s
                              WHERE e.id = s.eventId AND e.id = ?
                              GROUP BY e.id";
        $query_reserved_seats = "SELECT SUM(p.amount) + SUM(c.amount) as reservedSeats
                                 FROM seatCategories s, purchases p, carts c
                                 WHERE s.eventId = ? AND p.seatId = s.id AND p.eventId = s.id AND c.seatId = s.id
                                       AND c.eventId = s.id
                                 GROUP BY e.id";
        $free_seats = array();
        foreach ($event_ids as $event_id) {
            $stmt_total_seats = $this->prepare_bind_execute($query_total_seats, "i", $event_id);
            if ($stmt_total_seats === false) {
                return false;
            }
            $total_seats = -1;
            $stmt_total_seats->bind_result($total_seats);
            $stmt_total_seats->fetch();
            $stmt_total_seats->close();
            if ($total_seats === -1) {
                return false;
            }
            $stmt_reserved_seats = $this->prepare_bind_execute($query_reserved_seats, "i", $event_id);
            if ($stmt_reserved_seats === false) {
                return false;
            }
            $reserved_seats = -1;
            $stmt_reserved_seats->bind_result($reserved_seats);
            $stmt_reserved_seats->fetch();
            $stmt_reserved_seats->close();
            if ($reserved_seats === -1) {
                return false;
            }
            $free_seats[] = $total_seats - $reserved_seats;
        }
    }

    /*
     * Checks if the event with the given $event_id was created by the user which is currenly logged in.
     */
    private function is_logged_user_event_owner($event_id) {
        $event_info = $this->get_event_info($event_id);
        return $this->is_user_logged_in($event_info["promoterEmail"]);
    }

    /***************************/
    /***** CART FUNCTIONS ******/
    /***************************/
    /*
     * Insert a ticket into the logged user's cart, if such user is a customer. If problems arise, throws an exception.
     */
    public function put_tickets_into_cart(int $event_id, int $seat_category, int $amount) {
        $email = $this->get_logged_user_email();
        if ($email === false || !$this->isCustomer($email)) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $free_seats = $this->get_free_seat_tickets($event_id, $seat_category);
        if ($free_seats === false) {
            throw new Exception(QUERY_ERROR);
        }
        if ($free_seats < $amount) {
            return false;
        }
        $query = "INSERT INTO carts(eventId, seatId, amount, customerEmail)
                  VALUES ?, ?, ?, ?";
        $stmt = $this->prepare_bind_execute($query, "iiis", $event_id, $seat_category, $amount, $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(QUERY_ERROR);
        }
        return true;
    }

    /*
     * Remove a category of seats from the logged user's cart. If problems arise, throw exception.
     */
    public function remove_seat_category_from_cart(int $event_id, int $seat_category) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            throw new Exception(QUERY_ERROR);
        }
        $query = "DELETE FROM carts
                  WHERE customerEmail = ? AND eventId = ? AND seatId = ?";
        $stmt = $this->prepare_bind_execute($query, "sii", $email, $event_id, $seat_category);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(QUERY_ERROR);
        }
    }

    /*
     * Increments the number of tickets into the cart of the logged user. If it can't be made, because all tickets were
     * sold, it will return false, otherwise true. If problems arise, throw exception.
     */
    public function increment_seat_tickets(int $event_id, int $seat_category) {
        $free_seats = $this->get_free_seat_tickets($event_id, $seat_category);
        if ($free_seats === false) {
            throw new Exception(QUERY_ERROR);
        }
        if ($free_seats < 1) {
            return false;
        }
        return $this->change_tickets_into_cart($event_id, $seat_category, 1);
    }

    /*
     * Decrements the number of tickets into the cart of the logged user. If it can't be made, it will return false,
     * otherwise true. If problems arise, throw exception.
     */
    public function decrement_seat_tickets(int $event_id, int $seat_category) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            return false;
        }
        $query = "SELECT amount
                  FROM carts
                  WHERE seatId = ? AND eventId = ? AND customerEmail = ?";
        $stmt = $this->prepare_bind_execute($query, "iis", $seat_id, $event_id, $email);
        if ($stmt === false) {
            return false;
        }
        $reserved_seats = -1;
        $stmt->bind_result($reserved_seats);
        $stmt->fetch();
        $stmt->close();
        if ($reserved_seats < 1) {
            throw new Exception(QUERY_ERROR);
        } else if ($reserved_seats === 1) {
            $this->remove_seat_category_from_cart($event_id, $seat_category);
        } else {
            $this->change_tickets_into_cart($event_id, $seat_category, -1);
        }
    }

    /*
     * Buys a ticket, and eventually removes it from the logged user's cart. If problems arise, throws an exception.
     */
    public function buy_event_ticket(int $event_id, int $seat_category) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            throw new Exception(QUERY_ERROR);
        }
        $query = "INSERT INTO purchases(seatId, eventId, customerEmail, amount)
                  SELECT seatId, eventId, customerEmail, amount
                  FROM carts
                  WHERE seatId = ? AND eventId = ? AND customerEmail = ?";
        $stmt = $this->prepare_bind_execute($query, "iis", $event_id, $seat_category, $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new Exception(QUERY_ERROR);
        }
        $this->remove_seat_category_from_cart($event_id, $seat_category);
    }

    /*
     * Gets the remaining free seats for the given seat category in the given event. If something went wrong, returns
     * false.
     */
    private function get_free_seat_tickets(int $event_id, int $seat_category) {
        $query = "SELECT s.seats - SUM(p.amount) - SUM(c.amount) as freeSeats
                  FROM seatCategories s, purchases p, carts c
                  WHERE s.id = ? AND s.eventId = ? AND s.id = p.seatId AND s.eventId = p.eventId AND s.id = c.seatId
                        AND s.eventId = c.eventId
                  GROUP BY s.id, s.eventId, s.seats";
        $stmt = $this->prepare_bind_execute($query, "ii", $seat_category, $event_id);
        if ($stmt === false) {
            return false;
        }
        $free_seats = -1;
        $stmt->bind_result($free_seats);
        $stmt->fetch();
        $stmt->close();
        if ($free_seats === -1) {
            return false;
        }
        return $free_seats;
    }

    /*
     * Changes the amount of tickets of $change_amount into the logged user's cart with a specific seat category, if
     * such user is a customer. If problems arise, throws an exception.
     */
    private function change_tickets_into_cart(int $event_id, int $seat_category, int $change_amount) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            return false;
        }
        $query = "UPDATE seatCategories
                  SET amount = amount + ?
                  WHERE customerEmail = ? AND seatId = ? AND eventId = ?";
        $stmt = $this->prepare_bind_execute($query, "isii", $change_amount, $email, $seat_category, $event_id);
        if ($stmt === false) {
            return false;
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows !== 1;
    }

    /************************************/
    /***** NOTIFICATIONS FUNCTIONS ******/
    /************************************/
    /*
     * Returns the notifications sent to the currently logged user. If problems arise, it throws and exception.
     */ 
    public function get_logged_user_notifications() {
        $email = get_logged_user_email();
        if ($email === false) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "SELECT notificationId, dateTime, visualized, message
                  FROM usersNotifications, notifications
                  WHERE notificationId = id AND email = ?";
        $stmt = prepare_bind_execute($query, "s", $email);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    /*
     * Delete a logged user's notification, sent on $date_time and with type $notification_id. If problems arise, throws
     * exception.
     */
    public function delete_user_notification(int $notification_id, string $date_time) {
        $email = getLoggedUserEmail();
        if ($email === false) {
            throw new Exception(PRIVILEGE_ERROR);
        }
        $query = "DELETE FROM usersNotifications
                  WHERE notificationId = ? AND email = ? AND dateTime = ?";
        $stmt = $this->prepare_bind_execute($query, "iss", $notification_id, $email, $date_time);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows === -1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
        $stmt->close();
        $this->delete_unused_notification_types();
    }

    /*
     * Change the state (visualized or not) of the notification sent on $date_time and with the given type
     * $notification_id to the currently logged user. If problems arise, throws exception.
     */
    public function toggle_notification_view(int $notification_id, string $date_time) {
        $email = $this->get_logged_user_email();
        if ($email === false) {
            throw new Exception(QUERY_ERROR);
        }
        $query = "UPDATE usersNotifications
                  SET visualized = NOT visualized
                  WHERE email = ?
                  AND notificationId = ?
                  AND dateTime = ?";
        $stmt = $this->prepare_bind_execute($query, "sis", $email, $notification_id, $date_time);
        if ($stmt === false) {
            throw new Exception(QUERY_ERROR);
        } else if ($stmt->affected_rows === -1) {
            $stmt->close();
            throw new Exception(QUERY_ERROR);
        }
        $stmt->close();
    }

    /* 
     * Insert a new type of notification into the database. If problems arise, returns false.
     */
    private function insert_new_notification(string $message) {
        $query = "INSERT INTO notifications(message)
                  VALUES (?)";
        $stmt = $this->prepare_bind_execute($query, "s", $message);
        if ($stmt !== false) {
            $result = $stmt->affected_rows !== 1 ? false : $stmt->insert_id;
            $stmt->close();
            return $result;
        }
        return false;
    }

    /*
     * Send a notification with the given $message to the subscribers of the event with the given $event_id. If problems
     * arise, returns false.
     */
    // TODO: should be public? In that case, change the return false statement with an exception thrown
    private function send_notification_to_event_purchasers(int $event_id, string $message) {
        $notification_id = $this->insert_new_notification($message);
        if ($notification_id !== false) {
            $query = "INSERT INTO usersNotifications(email, dateTime, notificationId, visualized)
                      SELECT customerEmail, ?, ?, false
                      FROM purchases
                      WHERE eventId = ?";
            $stmt = $this->prepare_bind_execute($query, "ssi", date("Y-m-d H:i:s"), $notification_id, $event_id);
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
    private function delete_unused_notification_types() {
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
     * Checks if the given $email is associated to an user of the given $type. It returns false also in case of error.
     */
    private function is_user_type(string $email, $type) {
        $query = "SELECT *
                  FROM users
                  WHERE email = ? AND type = ?";
        $stmt = $this->prepare_bind_execute($query, "ss", $email, $type);
        return ($stmt !== false && $stmt->fetch() != null);
    }

    /*
     * Checks if the given $email is associated to a promoter user. It returns false also in case of error.
     */
    private function is_promoter(string $email) {
        return $this->is_user_type($email, PROMOTER_TYPE_CODE);
    }

    /*
     * Checks if the given $email is associated to a customer user. It returns false also in case of error.
     */
    private function is_customer(string $email) {
        return $this->is_user_type($email, CUSTOMER_TYPE_CODE);
    }

    /*
     * Checks if the given $email is associated to an admin user. It returns false also in case of error.
     */
    private function is_admin(string $email) {
        return $this->is_user_type($email, ADMIN_TYPE_CODE);
    }
    
    /*
     * Checks if the user on which the current operation is being done is the user that is currently logged in.
     */
    private function is_user_logged_in(string $email) {
        return $_SESSION["email"] === $email;
    }
    
    /*
     * Prepares a statement from the given query, and binds the arguments to the statement using the given binding
     * string. Then, the statement is executed and returned for use.
     * If something fails, false is returned.
     */
    private function prepare_bind_execute($query, $bindings, ...$arguments) {
        $stmt = $this->db->prepare($query);
        if ($stmt !== false) {
            $stmt->bind_param($bindings, $arguments);
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
