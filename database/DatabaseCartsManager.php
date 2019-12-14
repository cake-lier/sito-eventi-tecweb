<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once "database/DatabaseServiceManager.php" ;

/*
 * The class offering services regarding carts. It can put tickets into it, remove them, increment the ones associated
 * to a specific seat category or decrement them.
 */
class DatabaseCartsManager extends DatabaseServiceManager { 
    private const QUERY_ERROR = "An error occured while executing the query\n";
    private const PRIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so\n";
    /*
     *  Default constructor.
     */
    public function __construct(\mysqli $db) {
        parent::__construct($db);
    }
    /*
     * Get informations about all the tickets that the logged customer has put into the cart.
     */
    public function getLoggedUserTickets() {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "SELECT s.id AS seatId, s.eventId AS eventId, e.name AS eventName, e.place AS eventPlace, e.dateTime AS dateTime,
                         s.name as category, c.amount AS amount, s.price AS price
                  FROM events e, seatCategories s, carts c
                  WHERE c.seatId = s.id AND c.eventId = s.eventId AND s.eventId = e.id AND c.customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    /*
     * Insert a ticket into the logged user's cart, if such user is a customer. If the amount of tickets requested is
     * not available, returns false. If problems arise, throws an exception.
     */
    public function putTicketsIntoCart(int $eventId, int $seatCategory, int $amount) {
        $freeSeats = $this->getFreeSeatTickets($eventId, $seatCategory);
        if ($freeSeats === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        if ($freeSeats < $amount || $amount <= 0) {
            return false;
        }
        if (count(array_filter($this->getLoggedUserTickets(), function($e) use ($seatCategory, $eventId) {
                        return $e["seatId"] === $seatCategory && $e["eventId"] === $eventId;
                  })) > 0) {
            if (!$this->changeTicketsIntoCart($eventId, $seatCategory, $amount)) {
                throw new \Exception(self::QUERY_ERROR);
            }
        } else if(!$this->addTicketsIntoCart($eventId, $seatCategory, $amount)) {
            throw new \Exception(self::QUERY_ERROR);
        }
        return true;
    }
    /*
     * Remove a category of seats from the logged user's cart. If problems arise, throws an exception.
     */
    public function removeSeatCategoryFromCart(int $eventId, int $seatCategory) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "DELETE FROM carts
                  WHERE customerEmail = ? AND eventId = ? AND seatId = ?";
        $stmt = $this->prepareBindExecute($query, "sii", $email, $eventId, $seatCategory);
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
     * Increments the number of tickets into the cart of the logged user. If it can't be made, because all tickets were
     * sold, it will return false, otherwise true. If problems arise, throws an exception.
     */
    public function incrementSeatTickets(int $eventId, int $seatCategory) {
        $freeSeats = $this->getFreeSeatTickets($eventId, $seatCategory);
        if ($freeSeats === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        if ($freeSeats < 1) {
            return false;
        }
        return $this->changeTicketsIntoCart($eventId, $seatCategory, 1);
    }
    /*
     * Decrements the number of tickets into the cart of the logged user. If it can't be made, it will return false,
     * otherwise true. If problems arise, throws an exception.
     */
    public function decrementSeatTickets(int $eventId, int $seatCategory) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "SELECT amount
                  FROM carts
                  WHERE seatId = ? AND eventId = ? AND customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "iis", $seatCategory, $eventId, $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $reservedSeats = -1;
        $stmt->bind_result($reservedSeats);
        $stmt->fetch();
        $stmt->close();
        if ($reservedSeats < 1) {
            throw new \Exception(self::QUERY_ERROR);
        } else if ($reservedSeats === 1) {
            try {
                $this->removeSeatCategoryFromCart($eventId, $seatCategory);
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            if(!$this->changeTicketsIntoCart($eventId, $seatCategory, -1)) {
                throw new \Exception(self::QUERY_ERROR);
            }
        }
    }
    /*
     * Buys the logged user tickets, and removes them from the cart. If problems arise, throws an exception.
     */
    public function buyLoggedUserTickets() {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO purchases(seatId, eventId, customerEmail, amount)
                  SELECT seatId, eventId, customerEmail, amount
                  FROM carts
                  WHERE customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $removeQuery = "DELETE FROM carts
                        WHERE customerEmail = ?";
        $removeStmt = $this->prepareBindExecute($removeQuery, "s", $email);
        if ($removeStmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $removeRows = $removeStmt->affected_rows;
        $removeStmt->close();
        if ($removeRows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Gets the remaining free seats for the given seat category in the given event. If something went wrong, returns
     * false.
     */
    private function getFreeSeatTickets(int $eventId, int $seatCategory) {
        $query = "SELECT s.seats - SUM(IFNULL(p.amount, 0)) - SUM(IFNULL(c.amount, 0)) as freeSeats
                  FROM (seatCategories s LEFT OUTER JOIN purchases p ON s.id = p.seatId AND s.eventId = p.eventId)
                  LEFT OUTER JOIN carts c ON s.id = c.seatId AND s.eventId = c.eventId
                  WHERE s.id = ? AND s.eventId = ?
                  GROUP BY s.id, s.eventId, s.seats";
        $stmt = $this->prepareBindExecute($query, "ii", $seatCategory, $eventId);
        if ($stmt === false) {
            return false;
        }
        $freeSeats = -1;
        $stmt->bind_result($freeSeats);
        $stmt->fetch();
        $stmt->close();
        if ($freeSeats === -1) {
            return false;
        }
        return $freeSeats;
    }
    /*
     * Add to the cart of the logged user $amount tickets with the specified $eventId and $seatCategory.
     */
    private function addTicketsIntoCart(int $eventId, int $seatCategory, int $amount) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            return false;
        }
        $query = "INSERT INTO carts(eventId, seatId, amount, customerEmail)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->prepareBindExecute($query, "iiis", $eventId, $seatCategory, $amount, $email);
        if ($stmt === false) {
            return false;
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows === 1;
    }
    /*
     * Changes the amount of tickets of $changeAmount into the logged user's cart with a specific seat category, if
     * such user is a customer. If problems arise, throws an exception.
     */
    private function changeTicketsIntoCart(int $eventId, int $seatCategory, int $changeAmount) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            return false;
        }
        $query = "UPDATE carts
                  SET amount = amount + ?
                  WHERE customerEmail = ? AND seatId = ? AND eventId = ?";
        $stmt = $this->prepareBindExecute($query, "isii", $changeAmount, $email, $seatCategory, $eventId);
        if ($stmt === false) {
            return false;
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows === 1;
    }
}
?>
