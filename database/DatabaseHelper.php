<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;
require_once("./iDatabaseHelper.php");
require_once("./DatabaseNotificationsManager.php");
require_once("./DatabaseCartsManager.php");
require_once("./DatabaseUsersManager.php");
require_once("./DatabaseEventsManager.php");

/*
 * This class allows to use functionalities which needs to interact with the database, the one which parameters are
 * passed when creating this class. The functionalities are divided into four areas: "events", for manipulating
 * events like creating a new one, getting informations about them, etc; "users", for adding, removing users, changing
 * their data and get info about them; "carts", for managing everything connevted to the ticket purchase;
 * "notifications", to create, send and delete them. All these functions are accessible through three "managers" which
 * offer their services in their specific area.
 */
class DatabaseHelper implements iDatabaseHelper {
    private $eventsManager;
    private $usersManager;
    private $cartsManager;
    private $notificationsManager;

    /*
     * Constructor which needs the server name where the database is located, the username and password for accessing
     * the database and the database name itself for connecting to it. Only MySQL databases are supported.
     */
    public __construct(string $serverName, string $username, string $password, string $dbName) {
        $db = new mysqli($serverName, $username, $password, $dbName);
        if($db->connect_error) {
            die("Failed to connect to the database, error: " . $db->connect_error);
        }
        $this->notificationsManager = new DatabaseNotificationsManager($db);
        $this->eventsManager = new DatabaseEventsManager($db, $this->notificationsManager);
        $this->usersManager = new DatabaseUsersManager($db);
        $this->cartsManager = new DatabaseCartsManager($db);
    }
    /*
     * Returns the manager object for events functionalities.
     */
    public function getEventsManager() {
        return $this->eventsManager;
    }
     /*
     * Returns the manager object for users functionalities.
     */
    public function getUsersManager() {
        return $this->usersManager;
    }
    /*
     * Returns the manager object for carts functionalities.
     */
    public function getCartsManager() {
        return $this->cartsManager;
    }
    /*
     * Returns the manager object for notifications functionalities.
     */
    public function getNotificationsManager() {
        return $this->notificationsManager;
    }
}

?>
