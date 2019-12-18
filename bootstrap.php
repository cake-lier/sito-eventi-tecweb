<?php

use \it\unibo\tecweb\seatheat\DatabaseHelper;

session_start();

require_once "database/DatabaseHelper.php";
require_once "utils/functions.php";

define("SERVER_NAME", "localhost");
define("USER_NAME", "seatheat");
define("PASSWORD", "");
define("DATABASE", "my_seatheat");
define("IMG_DIR", "img/");
define("JS_DIR", "js/");
define ("LOG_FILE", "log.txt");

$dbh = new DatabaseHelper(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

?> 