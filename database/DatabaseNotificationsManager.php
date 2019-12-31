<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once("./database/DatabaseServiceManager.php");

/*
 * The class offering services regarding notifications. It can create, send, delete them and toggle the visualized
 * property of a notification.
 */
class DatabaseNotificationsManager extends DatabaseServiceManager { 
    private const QUERY_ERROR = "An error occured while executing the query";
    private const PRIVILEGE_ERROR = "The user performing the operation hasn't enough privileges to do so";
    /*
     *  Default constructor.
     */
    public function __construct(\mysqli $db) {
        parent::__construct($db);
    }
    /*
     * Returns the notifications sent to the currently logged user. If problems arise, throws and exception.
     */ 
    public function getLoggedUserNotifications() {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "SELECT notificationId, dateTime, visualized, message
                  FROM usersNotifications, notifications
                  WHERE notificationId = id AND email = ?";
        $stmt = $this->prepareBindExecute($query, "s", $email);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    /*
     * Delete a logged user's notification, sent on $dateTime and with type $notificationId. If problems arise, throws
     * an exception.
     */
    public function deleteUserNotification(int $notificationId, string $dateTime) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $query = "DELETE FROM usersNotifications
                  WHERE notificationId = ? AND email = ? AND dateTime = ?";
        $stmt = $this->prepareBindExecute($query, "iss", $notificationId, $email, $dateTime);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $this->deleteUnusedNotificationTypes();
    }
    /*
     * Change the state (visualized or not) of the notification sent on $dateTime and with the given type
     * $notificationId to the currently logged user. If problems arise, throws exception.
     */
    public function toggleNotificationView(int $notificationId, string $dateTime) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "UPDATE usersNotifications
                  SET visualized = NOT visualized
                  WHERE email = ?
                  AND notificationId = ?
                  AND dateTime = ?";
        $stmt = $this->prepareBindExecute($query, "sis", $email, $notificationId, $dateTime);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /*
     * Send a notification with the given $message to the subscribers of the event with the given $eventId. If problems
     * arise, throws an exception.
     */
    public function sendNotificationToEventPurchasers(int $eventId, string $message) {
        $notificationId = $this->insertNewNotification($message);
        if ($notificationId === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $query = "INSERT INTO usersNotifications(email, dateTime, notificationId, visualized)
                  SELECT DISTINCT customerEmail, CURRENT_TIMESTAMP, ?, false
                  FROM purchases
                  WHERE eventId = ?";
        $stmt = $this->prepareBindExecute($query, "ii", $notificationId, $eventId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /* 
     * Send a notification with the given $message to the admins 
     */
    public function sendNotificationToAdmin(string $message) {
        $email = $this->getLoggedUserEmail();
        if ($email === false) {
            throw new \Exception(self::PRIVILEGE_ERROR);
        }
        $message = "Inviato da ".$email.":\n".$message;
        $notificationId = $this->insertNewNotification($message);
        if ($notificationId === false) {
            throw new \Exception(self::QUERY_ERROR);
        }

        $query = "INSERT INTO usersNotifications(email, dateTime, notificationId, visualized)
                  SELECT email, CURRENT_TIMESTAMP, ?, false
                  FROM users
                  WHERE type = 'a'";
        $stmt = $this->prepareBindExecute($query, "i", $notificationId);
        if ($stmt === false) {
            throw new \Exception(self::QUERY_ERROR);
        }
        $rows = $stmt->affected_rows;
        $stmt->close();
        if ($rows === -1) {
            throw new \Exception(self::QUERY_ERROR);
        }
    }
    /* 
     * Insert a new type of notification into the database. If problems arise, returns false.
     */
    private function insertNewNotification(string $message) {
        $query = "INSERT INTO notifications(message)
                  VALUES (?)";
        $stmt = $this->prepareBindExecute($query, "s", $message);
        if ($stmt !== false) {
            $result = $stmt->affected_rows !== 1 ? false : $stmt->insert_id;
            $stmt->close();
            return $result;
        }
        return false;
    }
    /*
     * Deletes the notification types that are not used anymore.
     */
    private function deleteUnusedNotificationTypes() {
        $query = "DELETE FROM notifications
                  WHERE id NOT IN (SELECT notificationId
                                   FROM usersNotifications)";
        $result = $this->query($query); // No risk of SQL injection
        return $result;
    }
}

?>
