<?php
require_once 'System/Daemon.php';

// Set the working directory to the script directory
chdir(dirname(__FILE__));

// Setup System_Daemon
System_Daemon::setOption('appName', 'NewRecordChecker');
System_Daemon::setOption('appDir', dirname(__FILE__));
System_Daemon::setOption('authorEmail', 'your_email@example.com');

// Spawn the daemon
System_Daemon::start();

// Database connection settings

$dbHost = "159.65.239.175";
$dbUsername = "isengardappz";
$dbPassword = "!23qweASD##";
$dbName = "bongasms";
// $port = "3306";
// Connect to the database
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check for database connection errors
if ($conn->connect_error) {
    System_Daemon::log(System_Daemon::LOG_ERR, 'Database connection error: ' . $conn->connect_error);
    exit(1);
}

// Start the loop to check for new records
while (!System_Daemon::isDying()) {
    // Your query to check for new records, adjust this based on your table structure
    // $query = "SELECT * FROM batch_request WHERE status = 0 limit 1";
    $query = "SELECT * FROM batch_request limit 1";

    // Execute the query
    $result = $conn->query($query);

    // Check if there are new records
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Echo the message for each new record
            echo "New record found with ID: " . $row['batch_id'] . "\n";
        }
    }

    // Sleep for a specific interval (e.g., 5 seconds) before checking again
    sleep(2);
}

// Close the database connection when the loop ends
$conn->close();

// Stop the daemon
System_Daemon::stop();
