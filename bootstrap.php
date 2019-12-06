<?php
session_start();

require_once("./database/DatabaseHelper.php")

define("SERVER_NAME", "localhost");
define("USER_NAME", "seatheat");
define("PASSWORD", "");
define("DATABASE", "my_seatheat");

$dbh = new DatabaseHelper(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

?> 
