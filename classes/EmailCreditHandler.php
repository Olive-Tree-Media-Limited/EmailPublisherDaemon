<?php
require_once 'DatabaseUtilities.php'; // Include the DatabaseUtilities.php file
require_once("WorkerException.php");
// class EmailCreditHandler
// {
//     private $mysqli;
//     private $clientID;

//     public function __construct($mysqli, $clientID)
//     {
//         $this->mysqli = $mysqli;
//         $this->clientID = $clientID;
//     }

//     public function getClientEmailCredits()
//     {
//         $query = "SELECT email_credits FROM clients WHERE id = ?";
//         $stmt = DatabaseUtilities::prepareStatement($this->mysqli, $query);

//         $stmt->bind_param("i", $this->clientID);
//         DatabaseUtilities::executeStatement($stmt);


        $query = "SELECT email_credits FROM clients WHERE id = 1";
        $result = DatabaseUtilities::executeQuery($mysqli, $query);

        print_r($result);

//         $stmt->bind_result($emailCredits);
//         $stmt->fetch();
//         $stmt->close();
//         return $emailCredits;
//     }

//     public function updateClientEmailCredits($newEmailCredits)
//     {
//         $query = "UPDATE clients SET email_credits = ? WHERE id = ?";
//         $stmt = DatabaseUtilities::prepareStatement($this->mysqli, $query);
//         $stmt->bind_param("di", $newEmailCredits, $this->clientID);
//         DatabaseUtilities::executeStatement($stmt);
//         $stmt->close();
//     }


// }

// // Usage:
// $mysqli = DatabaseUtilities::getDatabaseConnection(); // Get the database connection
// $clientID = 1; // Replace with the actual client's ID
// $emailCost = 2.5; // Replace with the actual cost of the email

// $creditHandler = new EmailCreditHandler($mysqli, $clientID);
// $result = $creditHandler->sendEmail($emailCost);
// echo $result;

// Close the database connection (if needed)
// $mysqli->close();
?>
