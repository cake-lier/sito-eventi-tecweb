<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once("./database/DatabaseServiceManager.php");

/*
 * The class offering services regarding carts. It can put tickets into it, remove them, increment the ones associated
 * to a specific seat category or decrement them.
 */
class DatabaseCartsManager extends DatabaseServiceManager { 
    private const QUERY_ERROR = "An error occured while executing the query";
    private const PRIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so";
    /*
     *  Default constructor.
     */
    public function __construct(\mysqli $db) {
        DatabaseServiceManager::__construct($db);
    }
    /*
     * Insert a ticket into the logged user's cart, if such user is a customer. If the amount of tickets requested is
     * not available, returns false. If problems arise, throws an exception.
     */
    public function putTicketsIntoCart(int $eventId, int $seatCategory, int $amount) {
        $email = $this->getLoggedUserEmail();
        if ($email === false || !$this->isCustomer($email)) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $freeSeats = $this->getFreeSeatTickets($eventId, $seatCategory);
        if ($freeSeats === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        if ($freeSeats < $amount) {
            return false;
        }
        $query = "INSERT INTO carts(eventId, seatId, amount, customerEmail)
                  VALUES ?, ?, ?, ?";
        $stmt = $this->prepareBindExecute($query, "iiis", $eventId, $seatCategory, $amount, $email);
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
    public function decrementSeatTickets(int $seatId, int $eventId, int $seatCategory) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "SELECT amount
                  FROM carts
                  WHERE seatId = ? AND eventId = ? AND customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "iis", $seatId, $eventId, $email);
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
            $this->changeTicketsIntoCart($eventId, $seatCategory, -1);
        }
    }
    /*
     * Buys a ticket, and eventually removes it from the logged user's cart. If problems arise, throws an exception.
     */
    public function buyTickets(int $eventId, int $seatCategory) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO purchases(seatId, eventId, customerEmail, amount)
                  SELECT seatId, eventId, customerEmail, amount
                  FROM carts
                  WHERE seatId = ? AND eventId = ? AND customerEmail = ?";
        $stmt = $this->prepareBindExecute($query, "iis", $eventId, $seatCategory, $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows !== 1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        try {
            $this->removeSeatCategoryFromCart($eventId, $seatCategory);
        } catch (\Exception $e) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Gets the remaining free seats for the given seat category in the given event. If something went wrong, returns
     * false.
     */
    private function getFreeSeatTickets(int $eventId, int $seatCategory) {
        $query = "SELECT s.seats - SUM(p.amount) - SUM(c.amount) as freeSeats
                  FROM seatCategories s, purchases p, carts c
                  WHERE s.id = ? AND s.eventId = ? AND s.id = p.seatId AND s.eventId = p.eventId AND s.id = c.seatId
                        AND s.eventId = c.eventId
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
     * Changes the amount of tickets of $changeAmount into the logged user's cart with a specific seat category, if
     * such user is a customer. If problems arise, throws an exception.
     */
    private function changeTicketsIntoCart(int $eventId, int $seatCategory, int $changeAmount) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            return false;
        }
        $query = "UPDATE seatCategories
                  SET amount = amount + ?
                  WHERE customerEmail = ? AND seatId = ? AND eventId = ?";
        $stmt = $this->prepareBindExecute($query, "isii", $changeAmount, $email, $seatCategory, $eventId);
        if ($stmt === false) {
            return false;
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows !== 1;
    }
}

?>
