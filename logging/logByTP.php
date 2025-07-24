<?php

function beginLog(string $action): void
{
    error_log("----------------------------------------------------------------");
    error_log("begin ". $action);


    error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
    if (isset($_SESSION)) {
        error_log("SESSION VARIABLES:\n" . print_r($_SESSION, true));
    }
    error_log("POST VARIABLES:\n" . print_r($_POST, true));
    error_log("COOKIE VARIABLES:\n" . print_r($_COOKIE, true));
}

function endLog(string $error, string $action): void
{
    error_log($error);
    error_log("end ". $action);
    error_log("----------------------------------------------------------------\n\n");
}

function logException(string $action, Exception $e): void
{
    error_log($action." error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
}