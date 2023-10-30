<?php

# Database settings
// $hostnameOrIP = "127.0.0.1";
// $hostnameOrIP = "localhost";
// $userName = "root";
// $password = "DarthVader-2012";
// $dbName = "bongasms";
// $port = "3306";

$hostnameOrIP = "159.65.239.175";
$userName = "isengardappz";
$password = "!23qweASD##";
$dbName = "bongasms";
$port = "3306";


# Application settings
$appname = "custom_email_requests_app";
$author = "Kepha Okari";
$email = "kepha.okari@olivetreemobile.co";
$description = "Pick the email request logged in outgoing_emails table.";
$dir = dirname(__FILE__);
$executable = "requests.php";
$logfile = "/var/log/applications/customEmailRequestsApp.log";


## DO NOT EDIT BELOW THIS LINE ##

$options = array(
    "authorName" => $author,            # Author name
    "authorEmail" => $email,            # Author email
    "appName" => $appname,              # The application name
    "appDescription" => $description,   # Daemon description
    "appDir" => $dir,                   # The home directory of the daemon
    "appExecutable" => $executable,     # The executable daemon file
    "logLocation" => $logfile,          # Log file location
    "logPhpErrors" => "TRUE",           # Reroute PHP errors to log function
    "logFilePosition" => "TRUE",        # Show file in which the log message was generated
    "logLinePosition" => "TRUE",        # Show the line number in which the log message was generated
    "sysMaxExecutionTime" => "0",       # Maximum execution time of the script in seconds (0 is infinite)
    "sysMaxInputTime" => "0",           # Maximum time to spend parsing request data (0 is infinite)
    "sysMemoryLimit" => "128M"          # Maximum amount of memory the script may consume (0 is infinite)
);

?>
