<?php
/*****************************
|| Coded By Deepak Rathod      
|| For TwoHearts
*****************************/
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "db", "MysqliDb.php"));
$host = "localhost";
$database = "clufter";
$username = "root";
$password = "";
$db = new MysqliDb ($host, $username, $password, $database);
?>