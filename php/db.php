<?php
$dbHandler = null;
try {
    $dbHandler = new PDO("mysql:host=mysql;dbname=portfoliousers;charset=utf8", "root", "qwerty");

    $dbHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $ex) {
    exit("Database connection failed");
}
