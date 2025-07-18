<?php

require_once 'Config.php';

function connectToDatabase() {

    try {
        $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        error_log("Connection failed: ".$e->getMessage(). ", at: ". $e->getTraceAsString());
        die("Connection failed: " . $e->getMessage());
    }

}