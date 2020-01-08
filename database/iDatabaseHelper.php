<?php

declare(strict_types = 1);
namespace it\unibo\tecweb\seatheat;

/*
 * This interface allows to use functionalities which needs to interact with the database. The functionalities are
 * divided into four areas: "events", for manipulating events like creating a new one, getting informations about them,
 * etc; "users", for adding, removing users, changing their data and get info about them; "carts", for managing
 * everything connevted to the ticket purchase; "notifications", to create, send and delete them. All these functions
 * are accessible through three "managers" which offer their services in their specific area.
 */
interface iDatabaseHelper {
    /*
     * Returns the manager object for events functionalities.
     */
    public function getEventsManager();
     /*
     * Returns the manager object for users functionalities.
     */
    public function getUsersManager();
    /*
     * Returns the manager object for carts functionalities.
     */
    public function getCartsManager();
    /*
     * Returns the manager object for notifications functionalities.
     */
    public function getNotificationsManager();
}

?> 
