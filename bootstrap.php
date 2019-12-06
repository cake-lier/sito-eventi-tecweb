<?php
session_start();

require_once("./database/DatabaseHelper.php");

define("SERVER_NAME", "localhost");
define("USER_NAME", "seatheat");
define("PASSWORD", "");
define("DATABASE", "my_seatheat");
define("IMG_DIR", "img");

$dbh = new \it\unibo\tecweb\seatheat\DatabaseHelper(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

?> 