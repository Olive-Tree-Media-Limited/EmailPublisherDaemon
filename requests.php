#!/usr/bin/php
<?php

require_once("config.php");

require_once("classes/DatabaseUtilities.php");
require_once("classes/PHPDaemon.php");
require_once("classes/MySQL.php");
require_once("classes/SQLException.php");
require_once("classes/WorkerException.php");

require_once("System/Daemon.php");

/**
 * Convert errors into exceptions.
 *
 * NOTE: If an exception is not caught, a PHP _Fatal Error_ will be issued with
 * an "Uncaught Exception". This means that your program will be terminated.
 *
 * @param int $errno the level of the error raised
 * @param string $errstr the error message
 * @param string $errfile the filename that the error was raised in
 * @param int $errline the line number the error was raised at
 */
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() == 0) {
        // Error reporting is currently turned off or suppressed with @
        return;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// Set the daemon options
System_Daemon::setOptions($options);

// Spawn Deamon
System_Daemon::start();

set_error_handler("exception_error_handler");

date_default_timezone_set("Africa/Nairobi");

define("ERROR_LOG", $logfile);

error_log("[INFO: ". date("Y-m-d H:i:s") . "] ". "Daemon: 'PHPDaemon' "
        . "started\n", 3, ERROR_LOG);

// Create a new PHPDaemon object
$phpDaemon = new PHPDaemon();

while (!System_Daemon::isDying()) {
    try {
        $mysql = new MySQL($hostnameOrIP, $userName, $password, $dbName, $port);

        // Turn off autocommit
        DatabaseUtilities::setAutoCommit($mysql->mysqli, FALSE);

        // Process
        $phpDaemon->process($mysql->mysqli, ERROR_LOG);

        // Commit changes after processing successfully
        DatabaseUtilities::commit($mysql->mysqli);
    } catch (SQLException $e) {
        error_log("ERROR :: ".date("y-m-d H:i:s"). " SQL Exception :: "
                . $e->__toString(). "\n", 3, ERROR_LOG);
    } catch (WorkerException $e) {
        error_log("ERROR :: ".date("y-m-d H:i:s"). " Worker Exception :: "
                . $e->__toString(). "\n", 3, ERROR_LOG);
    } catch (Exception $e) {
        error_log("ERROR :: ".date("y-m-d H:i:s"). " General Exception :: "
                . $e->__toString(). "\n", 3, ERROR_LOG);
    }

    /*
     * Relax the system by sleeping for a little bit.
     * (iterate also clears statcache).
     */
    System_Daemon::iterate(2);
}

System_Daemon::stop();
?>