<?php

require_once 'Config.php';
require_once dirname(__DIR__, 1) .'/logging/logByTP.php';

function connectToDatabase(): PDO
{
    try {
        $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        logException("Connection", $e);
        throw new PDOException("Connection failed: " . $e->getMessage());
    }

}