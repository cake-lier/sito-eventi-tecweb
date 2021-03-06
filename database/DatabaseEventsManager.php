<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once "database/DatabaseServiceManager.php";
require_once "database/DatabaseNotificationsManager.php";

class DatabaseEventsManager extends DatabaseServiceManager {
    private const QUERY_ERROR = "An error occured while executing the query\n";
    private const PRIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so\n";
    private const DATE_ERROR = "The date should be a future date from now\n";
    
    private $notificationsManager;
    /*
     *  Default constructor.
     */
    public function __construct(\mysqli $db, DatabaseNotificationsManager $notificationsManager) {
        parent::__construct($db);
        $this->notificationsManager = $notificationsManager;
    }
    /*
     * Returns the id of the most popular event, intended as the one which sold more tickets but is still not sold out, with a
     * date in the future. If no event is present, returns false. Throws an exception if something went wrong.
     */
    public function getMostPopularEvent() {
        try {
            $eventIds = $this->getEventIdsFiltered();
            $freeSeats = $this->getEventsFreeSeats(...$eventIds);
            if (count($eventIds) > 0) {
                $events = array();
                array_walk($eventIds, function($e, $i) use (&$freeSeats, &$events) {
                    $events[] = ["id" => $e, "freeSeats" => $freeSeats[$i]];
                });
                usort($events, function($fst, $snd) {
                    return $snd["freeSeats"] - $fst["freeSeats"];
                });
                return $events[0]["id"];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /*
     * Returns the id of the most recent event, intended as the one which was added to the database last, with a date in the
     * future. Throws an exception if something went wrong.
     */
    public function getMostRecentEvent() {
        $query = "SELECT e.id AS id
                  FROM events e
                  WHERE e.dateTime >= CURRENT_TIMESTAMP
                  ORDER BY e.id DESC
                  LIMIT 1";
        // No risk of SQL injection 
        $result = $this->query($query);
        if ($result === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $events = $result->fetch_assoc();
        if ($events !== null) {
            $id = intval($events["id"]);
            $result->close();
            return $id;
        }
        return false;
    }
    /*
     * Returns info about the event with the given $eventId. Throws an exception if something went wrong.
     */
    public function getEventInfo(int $eventId) {
        $query = "SELECT e.id AS id, e.name AS name, e.place AS place, e.dateTime AS dateTime, e.description AS description,
                         e.site AS site, p.organizationName AS organizationName, e.promoterEmail AS promoterEmail,
                         CAST(SUM(s.seats) AS INT) AS totalSeats
                  FROM events e, promoters p, seatCategories s
                  WHERE e.id = ? AND e.promoterEmail = p.email AND s.eventId = e.id
                  GROUP BY e.name, e.place, e.dateTime, e.description, e.site, p.organizationName, e.promoterEmail";
        $stmt = $this->prepareBindExecute($query, "i", $eventId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $event = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $freeSeats = $this->getEventsFreeSeats($eventId);
        if ($freeSeats === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $event["freeSeats"] = $freeSeats[0];
        $catQuery = "SELECT ec.name AS name
                      FROM events e, eventCategories ec, eventsToCategories etc
                      WHERE e.id = ? AND e.id = etc.eventId AND etc.categoryId = ec.id";
        $catStmt = $this->prepareBindExecute($catQuery, "i", $eventId);
        if ($catStmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $event["categories"] = array_column($catStmt->get_result()->fetch_all(MYSQLI_ASSOC), "name");
        $catStmt->close();
        return $event;
    }
    /*
     * Returns informations about seat categories for the event with given $eventId.
     */
    public function getEventSeatCategories(int $eventId) {
        $query = "SELECT s.id, s.eventId, s.name AS name, CAST(s.price AS FLOAT) AS price, s.seats AS seats,
                         CAST(SUM(IFNULL(p.amount, 0)) + SUM(IFNULL(c.amount, 0)) AS INT) AS occupiedSeats
                  FROM (seatCategories s LEFT OUTER JOIN purchases p ON s.id = p.seatId AND s.eventId = p.eventId)
                       LEFT OUTER JOIN carts c ON c.seatId = s.id AND c.eventId = s.eventId
                  WHERE s.eventId = ?
                  GROUP BY s.id, s.eventId, s.name, s.price, s.seats";
        $stmt = $this->prepareBindExecute($query, "i", $eventId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    /*
     * Returns informations about a specific seat category with this $seatId for the event with given $eventId.
     */
    public function getSeatInfo(int $eventId, int $seatId) {
        $query = "SELECT s.name AS name, CAST(s.price AS FLOAT) AS price, s.seats AS seats,
                         CAST(SUM(IFNULL(p.amount, 0)) + SUM(IFNULL(c.amount, 0)) AS INT) AS occupiedSeats
                  FROM (seatCategories s LEFT OUTER JOIN purchases p ON s.id = p.seatId AND s.eventId = p.eventId)
                       LEFT OUTER JOIN carts c ON c.seatId = s.id AND c.eventId = s.eventId
                  WHERE s.eventId = ? AND s.id = ?
                  GROUP BY s.id, s.eventId, s.name, s.price, s.seats";
        $stmt = $this->prepareBindExecute($query, "ii", $eventId, $seatId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }
    /*
     * Returns all the possible places for the events. Throws an exception if something went wrong.
     */
    public function getEventsPlaces() {
        $query = "SELECT DISTINCT place
                  FROM events";
        // No risk of SQL injection
        $result = $this->query($query);
        if ($result === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        return array_column($data, "place");
    }
    /*
     * Returns the number of events currently disponible to purchasing.
     */
    public function getEventsCount(string $keyword = "", bool $free = true, string $place = "", string $date = "",
                                   string $promoterEmail = "") {
        $condition = "";
        $bindings = "";
        $parameters = array();
        if ($place !== "") {
            $condition .= " AND place = ?";
            $bindings = "s";
            $parameters[] = $place;
        }
        if ($date !== "") {
            $condition .= " AND e.dateTime = ?";
            $bindings = $bindings . "s";
            $parameters[] = $date;
        }
        if ($keyword !== "") {
            $condition .= " AND INSTR(e.name, ?) > 0";
            $bindings = $bindings . "s";
            $parameters[] = $keyword;
        }
        if ($promoterEmail !== "") {
            $condition .= " AND promoterEmail = ?";
            $bindings = $bindings . "s";
            $parameters[] = $promoterEmail;
        }
        $query = "SELECT COUNT(DISTINCT e.id) AS num
                  FROM events e";
        if ($free) {
            $query .= ", seatCategories s";
            $condition .= " AND e.id = s.eventId
                            GROUP BY e.id, e.name
                            HAVING SUM(s.seats) > (SELECT IFNULL(SUM(IFNULL(p.amount, 0)) + SUM(IFNULL(c.amount, 0)), 0)
                                                   FROM events e1, seatCategories s1, purchases p, carts c
                                                   WHERE e1.id = e.id AND p.seatId = s1.id AND p.eventId = s1.eventId 
                                                         AND c.seatId = s1.id AND c.eventId = s1.eventId
                                                         AND s1.eventId = e1.id)";
        }
        $query .= " WHERE e.dateTime >= CURRENT_TIMESTAMP" . $condition;
        if ($free) {
            $query = "SELECT SUM(num) AS num FROM (" . $query . ") AS X";
        }
        $stmt = $this->prepareBindExecute($query, $bindings, ...$parameters);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result["num"];
    }
    /*
     * Returns the number of events already purchased by the currently logged customer.
     */
    public function getPurchasedEventsCount() {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "SELECT COUNT(DISTINCT eventId) AS num
                  FROM purchases
                  WHERE customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data["num"];
    }
    /*
     * Returns the number of events created by the currently logged promoter.
     */
    public function getCreatedEventsCount() {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isPromoter($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "SELECT COUNT(*) AS num
                  FROM events
                  WHERE promoterEmail = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data["num"];
    }
    /*
     * Returns all the ids of the events in the given $place, or on the given $date, or with free or not seats, or with a title
     * containing a specific keyword, or organized by a specific promoter. If the specified keyword is an empty string, this 
     * method will ignore the keyword constraint.
     * Every argument is optional: if not passed, the specific constraint won't be used for the search into the database, except
     * for the "$free" parameter which, if not specified, will make the method to return only events with still free seats.
     * It can be specified the range of results needed. Throws an exception if something went wrong.
     */
    public function getEventIdsFiltered(int $min = -1, int $max = -1, string $keyword = "", bool $free = true,
                                        string $place = "", string $date = "", string $promoterEmail = "") {
        $condition = "";
        $bindings = "";
        $parameters = array();
        if ($place !== "") {
            $condition .= " AND place = ?";
            $bindings = "s";
            $parameters[] = $place;
        }
        if ($date !== "") {
            $condition .= " AND e.dateTime = ?";
            $bindings = $bindings . "s";
            $parameters[] = $date;
        }
        if ($keyword !== "") {
            $condition .= " AND INSTR(e.name, ?) > 0";
            $bindings = $bindings . "s";
            $parameters[] = $keyword;
        }
        if ($promoterEmail !== "") {
            $condition .= " AND promoterEmail = ?";
            $bindings = $bindings . "s";
            $parameters[] = $promoterEmail;
        }
        if ($free) {
            $condition .= " AND e.id = s.eventId
                            GROUP BY e.id, e.name
                            HAVING SUM(s.seats) > (SELECT IFNULL(SUM(IFNULL(p.amount, 0)) + SUM(IFNULL(c.amount, 0)), 0)
                                                   FROM events e1, seatCategories s1, purchases p, carts c
                                                   WHERE e1.id = e.id AND p.seatId = s1.id AND p.eventId = s1.eventId 
                                                         AND c.seatId = s1.id AND c.eventId = s1.eventId
                                                         AND s1.eventId = e1.id)";
        }
        $query = "SELECT e.id AS id, e.name AS name
                  FROM events e, seatCategories s
                  WHERE e.dateTime >= CURRENT_TIMESTAMP";
        if (!$free) {
            $condition .= " GROUP BY e.id, e.name";
        }
        if ($min !== -1 && $max !== -1) {
            $condition .= " LIMIT ?, ?";
            $bindings .= "ii";
            array_push($parameters, $min, $max);
        }
        $query .= $condition;
        $stmt = $this->prepareBindExecute($query, $bindings, ...$parameters);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return array_column($result, "id");
    }
    /*
     * Checks if the event with the given $eventId is associated with a category passed.
     */
    public function hasEventCategories(int $eventId, string ...$categories) {
        if (count($categories) === 0) {
            return true;
        }
        $query = "SELECT COUNT(*)
                  FROM eventsToCategories etc, eventCategories ec
                  WHERE etc.eventId = ? AND etc.categoryId = ec.id AND (ec.name = ?";
        $bindings = "is";
        for ($i = 0; $i < count($categories) - 1; $i++) {
            $query .= " OR ec.name = ?";
            $bindings .= "s";
        }
        $query .= ")";
        $stmt = $this->prepareBindExecute($query, $bindings, $eventId, ...$categories);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $count = -1;
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        return $count > 0;
    }
    /*
     * Inserts a new event in the database, by the promoter currently logged in. If problems arise, or if the logged
     * user is not a promoter, throws an exception. It returns the id of the new event.
     */
    public function createEvent(string $name, string $place, string $dateTime, string $description,
                                string $promoterEmail, array $seatCategories, array $eventCategories,
                                string $site = null) {
        $email = $this->getLoggedUserEmail();
        // Only promoters can add events
        if ($email === false || !$this->isPromoter($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "INSERT INTO events(name, place, dateTime, description, site, promoterEmail)
                  VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $eventDate = new \DateTime($dateTime);
            if ($eventDate <= new \DateTime()) {
                throw new \Exception(self::DATE_ERROR);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $stmt = $this->prepareBindExecute($query, "ssssss", $name, $place, $dateTime, $description, $site,
                                          $promoterEmail);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        if ($rows !== 1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $eventId = $stmt->insert_id;
        $stmt->close();
        $seatQuery = "INSERT INTO seatCategories(eventId, name, seats, price)
                      VALUES (?, ?, ?, ?)";
        foreach ($seatCategories as $seatCategory) {
            $seatStmt = $this->prepareBindExecute($seatQuery, "isii", $eventId, $seatCategory["name"],
                                                  $seatCategory["seats"], $seatCategory["price"]);
            if ($seatStmt === false) {
                throw new \Exception(self::QUERY_ERROR);
            }
            $seatRows = $seatStmt->affected_rows;
            $seatStmt->close();
            if ($seatRows !== 1) {
                throw new \Exception(self::QUERY_ERROR);
            }
        }
        $categoriesQuery = "INSERT INTO eventCategories(name)
                            VALUES (?)";
        $categoryIdQuery = "SELECT id
                            FROM eventCategories
                            WHERE name = ?
                            LIMIT 1";
        $categoriesEventsQuery = "INSERT INTO eventsToCategories(eventId, categoryId)
                                  VALUES (?, ?)";
        foreach ($eventCategories as $eventCategory) {
            $categoryIdStmt = $this->prepareBindExecute($categoryIdQuery, "s", $eventCategory);
            if ($categoryIdStmt === false) {
                throw new \Exception(self::QUERY_ERROR);
            }
            $categoryId = -1;
            $ids = $categoryIdStmt->get_result();
            if ($ids->num_rows >= 1) {
                /* already exists */
                $categoryId = $ids->fetch_assoc()["id"];
                if ($categoryId === -1) {
                    throw new \Exception(self::QUERY_ERROR);
                }
            } else {
                $categoriesStmt = $this->prepareBindExecute($categoriesQuery, "s", $eventCategory);
                if ($categoriesStmt === false) {
                    throw new \Exception(self::QUERY_ERROR);
                }
                if ($categoriesStmt->affected_rows === 1) {
                    $categoryId = $categoriesStmt->insert_id;
                } else {
                    throw new \Exception(self::QUERY_ERROR);
                }
            }
            $categoryIdStmt->close();
            $categoriesEventsStmt = $this->prepareBindExecute($categoriesEventsQuery, "ii", $eventId, $categoryId);
            if ($categoriesEventsStmt === false) {
                throw new \Exception(self::QUERY_ERROR);
            }
            $rowsCategoriesEvents = $categoriesEventsStmt->affected_rows;
            $categoriesEventsStmt->close();
            if ($rowsCategoriesEvents !== 1) {
                throw new \Exception(self::QUERY_ERROR);
            }
        }
        return $eventId;
    }
    /* 
     * Returns the emails of the buyers of a certain event. Throws an exception if something went wrong.
     */
    public function getBuyers(int $eventId) {
        $query = "SELECT DISTINCT customerEmail as email
                  FROM purchases
                  WHERE eventId = ?";
        $stmt = $this->prepareBindExecute($query, "i", $eventId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    /* 
     * Returns the events the logged user has bought. Throws an exception if something went wrong.
     */
    public function getPurchasedEvents() {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "SELECT DISTINCT e.id AS id, e.name AS name, e.place AS place, e.dateTime AS dateTime,
                                  e.description AS description, e.site AS site, pr.organizationName AS organizationName
                  FROM events e, purchases p, promoters pr
                  WHERE p.customerEmail = ? AND e.id = p.eventId AND e.promoterEmail = pr.email";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    /*
     * Changes the date of the given event, sending to the users the given message, if the user logged in is the owner
     * of the event. If problems arise, or the logged user is not the owner of the event, throws an exception.
     */
    public function changeEventDate(int $eventId, string $newDate, string $notificationMessage) {
        if (!$this->isLoggedUserEventOwner($eventId)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "UPDATE events
                  SET dateTime = ?
                  WHERE id = ?";
        $stmt = $this->prepareBindExecute($query, "si", $newDate, $eventId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $this->notificationsManager->sendNotificationToEventPurchasers($eventId, $notificationMessage);
    }
    /*
     * Gets the remaining free seats for every event which event id was passed as a parameter.
     */
    private function getEventsFreeSeats(int ...$eventIds) {
        $queryTotalSeats = "SELECT SUM(seats) as totalSeats
                            FROM seatCategories
                            WHERE eventId = ?
                            GROUP BY eventId";
        $queryReservedSeats = "SELECT IFNULL(SUM(IFNULL(p.amount, 0)) + SUM(IFNULL(c.amount, 0)), 0) as reservedSeats
                               FROM (seatCategories s LEFT OUTER JOIN purchases p ON s.id = p.seatId AND s.eventId = p.eventId)
                                    LEFT OUTER JOIN carts c ON c.seatId = s.id AND c.eventId = s.eventId
                               WHERE s.eventId = ?
                               GROUP BY s.eventId";
        $freeSeats = array();
        foreach ($eventIds as $eventId) {
            $stmtTotalSeats = $this->prepareBindExecute($queryTotalSeats, "i", $eventId);
            if ($stmtTotalSeats === false) {
                return false;
            }
            $totalSeats = -1;
            $stmtTotalSeats->bind_result($totalSeats);
            $stmtTotalSeats->fetch();
            $stmtTotalSeats->close();
            $totalSeats = intval($totalSeats);
            if ($totalSeats === -1) {
                return false;
            }
            $stmtReservedSeats = $this->prepareBindExecute($queryReservedSeats, "i", $eventId);
            if ($stmtReservedSeats === false) {
                return false;
            }
            $reservedSeats = -1;
            $stmtReservedSeats->bind_result($reservedSeats);
            $stmtReservedSeats->fetch();
            $stmtReservedSeats->close();
            $reservedSeats = intval($reservedSeats);
            if ($reservedSeats === -1) {
                return false;
            }
            $freeSeats[] = $totalSeats - $reservedSeats;
        }
        return $freeSeats;
    }
    /*
     * Checks if the event with the given $eventId was created by the user which is currenly logged in.
     */
    public function isLoggedUserEventOwner(int $eventId) {
        try {
            $eventInfo = $this->getEventInfo($eventId);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this->isUserLoggedIn($eventInfo["promoterEmail"]);
    }
}

?>
