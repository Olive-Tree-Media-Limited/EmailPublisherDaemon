<?php
require_once("DatabaseUtilities.php");
require_once("WorkerException.php");
require_once "PepipostEmailSender.php";

/**
 * Script to process something.
 */
class PHPDaemon
{
    /**
     * Constructor.
     */
    function __construct()
    {
        // Initialise things here
    }

    /**
     * Processes something.
     *
     * @param object $mysqli MySQL connection object
     * @param string $log Path to the log file
     *
     * @return void
     */
    public function process($mysqli, $log)
    // {
    //     error_log("INFO :: ".date("y-m-d H:i:s"). " About to get the unprocessed requests.\n", 3, $log);

    //     // Start a transaction
    //     $mysqli->begin_transaction();
    //     $SENDER = null;
    //     try {
    //         // Build the query to fetch unprocessed requests
    //         $query = "SELECT * FROM email_requests WHERE status = 1 LIMIT 5";
    //         $result = DatabaseUtilities::executeQuery($mysqli, $query);

    //         if (count($result) == 0) {

    //             error_log("INFO :: ".date("y-m-d H:i:s")." -- No record found -- \n\n", 3, $log);

    //         } else {

    //             error_log("INFO :: " . date("y-m-d H:i:s") . " -- [*] " . count($result) . " REQUESTS FOUND --\n", 3, $log);
            
    //             $insertValues = [];
    //             $cc =[];
    //             $bcc = [];

    //             foreach ($result as $row) {
    //                 $batchID = $row["id"];
    //                 $client_id = $row["client_id"];
    //                 $subject = $row["subject"];
    //                 $sender = $row["sender"];
    //                 $recipients = explode(",", $row["recipient"]);
    //                 $body = $row["body"];
    //                 // $secret_key = $row["secret_key"];
    //                 $sender_name = $row["sender_name"];
    //                 $recipient_name = $row["recipient_name"];
    //                 // $attachments = $row["attachments"];
    //                 // $attributes = $row["attributes"];
    //                 $attributes = json_decode($row["attributes"], true);
    //                 $cc = $row["cc"];
    //                 $bcc = $row["bcc"];

    //                 $schedule = $row["schedule"];
    //                 $status = $row["status"];
    //                 $unique_code = $row["unique_code"];
                    
    //                 foreach ($recipients as $to_email) {
    //                     $api_key = "3d65cd3b2a075c359e9751336ec51af5";
    //                     $pepipostSender = new PepipostEmailSender($api_key);

    //                     $fromEmail = $sender;
    //                     $fromName = $sender_name;
    //                     $toEmail = $to_email;
    //                     $toName = $recipient_name;
    //                     // $subject = "copies Test PLEASE IGNORE";
    //                     $htmlContent = $body;

    //                     $attachments = [
    //                         // ["name" => "SamplePDFFile_5mb.pdf", "content" => "base64_encoded_file_content"],
    //                         // ["name" => "SampleCSVFile_119kb.pdf", "content" => "base64_encoded_file_content"]
    //                     ];
                        
    //                     // $attributes = [
    //                     //     "LEAD" => "Khaligraph Jones",
    //                     //     "BAND" => "Sauti Sol"
    //                     // ];

    //                     $cc = [
    //                         // ["email" => "rkapps47@gmail.com"],
    //                         // ["email" => "kephaokari@gmail.com"],
    //                     ];

    //                     if (!empty($row["cc"])) {
    //                         foreach (explode(',', $row["cc"]) as $cc_email) {
    //                             $cc[] = ["email" => trim($cc_email)];
    //                         }
    //                     }
                        
    //                     $bcc = [
    //                         // ["email" => "kephaokari@gmail.com"],
    //                         // ["email" => "kepha.okari@olivetreemobile.com"],
    //                     ];

    //                     if (!empty($row["bcc"])) {
    //                         foreach (explode(',', $row["bcc"]) as $bcc_email) {
    //                             $bcc[] = ["email" => trim($bcc_email)];
    //                         }
    //                     }
               
    //                     // $schedule = time() + 3;
    //                     // $schedule = null;

    //                     # check for email credits
    //                     $queryEmailCredits = "SELECT email_credits_cr, email_credits_dr, email_credits FROM clients WHERE id = ". $client_id;
    //                     $emailCredits = DatabaseUtilities::executeQuery($mysqli, $queryEmailCredits);
    //                     foreach ($emailCredits as $row) {
    //                         // Customize this part to format your data as needed
    //                         $email_credits_cr=$row["email_credits_cr"];
    //                         $email_credits_dr=$row["email_credits_dr"];
    //                         $email_credits=$row["email_credits"];
    //                         $output = "CR: {$email_credits_cr}, DR: {$email_credits_dr}, Credits: {$email_credits}\n";
    //                     }
                        
    //                     $mergedArray = array_merge($cc, $bcc);
    //                     $totalCount = count($recipients) + count($mergedArray);

    //                     if($email_credits >= $totalCount){

                                                 
    //                         $debitCreditsQuery = "UPDATE clients
    //                             SET email_credits_dr = email_credits_dr + $totalCount,
    //                             email_credits = email_credits - $totalCount
    //                             WHERE id = $client_id";
    //                         DatabaseUtilities::executeQuery($mysqli, $debitCreditsQuery);
    
    //                         error_log("INFO :: " . date("y-m-d H:i:s") . " -- processed Response--\n".$totalCount ." EMAILS BILLED\n", 3, $log);


    //                         $emailResp = $pepipostSender->sendEmail(
    //                             $fromEmail,
    //                             $fromName,
    //                             $toEmail,
    //                             $toName,
    //                             $subject,
    //                             $htmlContent,
    //                             $attributes,
    //                             $attachments,
    //                             $bcc,
    //                             $cc,
    //                             $schedule
    //                         );

    //                         error_log("INFO :: " . date("y-m-d H:i:s") . " -- processed Response--\n".$emailResp, 3, $log);
    //                         # Handle the Billing
    //                         $responseData = json_decode($emailResp, true);
    //                         $status_message = "processed";
    //                         $updateQuery = "UPDATE email_requests SET provider_response = '".$emailResp."', status_message = '".$status_message."', send_attempts =  send_attempts + 1 WHERE id = ".$batchID;
    //                         DatabaseUtilities::executeQuery($mysqli, $updateQuery);
                            
    //                         error_log("INFO :: " . date("y-m-d H:i:s") . " -- STATUS MESSAGE: ".$status_message." \n", 3, $log);

       
    //                     } else {
    //                         # insufficient balance
    //                         $status_message = "Insufficient email credits";
    //                         $updateQuery = "UPDATE email_requests 
    //                             SET status_message = '" . $status_message . "' 
    //                             WHERE id = " . $batchID;
    //                         DatabaseUtilities::executeQuery($mysqli, $updateQuery);
    //                         error_log("INFO :: " . date("y-m-d H:i:s") . " -- STATUS MESSAGE: ".$status_message." \n", 3, $log);

    //                         $mysqli->commit();
    //                     }

    //                     $updateQuery = "UPDATE email_requests SET status = 2 WHERE id = ".$batchID;
    //                     DatabaseUtilities::executeQuery($mysqli, $updateQuery);
    //                     $mysqli->commit();

    //                     error_log("INFO :: " . date("y-m-d H:i:s") . "Requests marked as processed\n", 3, $log);


    //                 }
    //             }
            
    //         }
            
    //     } catch (Exception $e) {
    //         // Rollback the transaction in case of an exception
    //         error_log("ERROR :: ".date("y-m-d H:i:s")." -- Exception occurred: ".$e->getMessage()."\n", 3, $log);
    //     }
    // }
    {
        try {
            error_log("INFO :: " . date("y-m-d H:i:s") . " Connecting to RabbitMQ" . "\n", 3, $log);
            $connection = new AMQPStreamConnection('172.17.0.2', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            // $CHANNEL_NAME = "send_bulk_email_exchange";
            $CHANNEL_NAME = "sendBulkEmailsExchange";
            $channel->exchange_declare($CHANNEL_NAME, 'topic', false, true, false);

            error_log("INFO :: " . date("y-m-d H:i:s") . " CONNECTED to RabbitMQ" . "\n", 3, $log);

            if ($mysqli->connect_errno) {
                throw new Exception("Failed to connect to MySQL: " . $mysqli->connect_error);
            }

            error_log("INFO :: " . date("y-m-d H:i:s") . " About to get the unprocessed sms blasts." . "\n", 3, $log);

            $mysqli->begin_transaction();

            $query = "SELECT * FROM outgoing_bulk_emails WHERE status = 1 LIMIT 5";
            $result = DatabaseUtilities::executeQuery($mysqli, $query);

            if (count($result) > 0) {
                $count = 0;
                $dataList = []; // Prepare an array to hold all data arrays
                $idsToUpdate = []; // Prepare an array to hold the IDs for updating status

                error_log("INFO :: " . date("y-m-d H:i:s") . " Preparing objects for queueing\n", 3, $log);


                foreach ($result as $row) {
                    $dataList[] = [
                        "id" => $row['id'],
                        "message" => $row['message'],
                        "api_key" => $row['api_key'],
                        "bulk_blast_id" => $row['bulk_blast_id'],
                        "apiClientID" => $row['client_id'],
                        "service_id" => $row['service_id'],
                        "email_from" => $row['email_from'],
                        "email_to" => $row['email_to'],
                        "email_subject" => $row['email_subject'],
                        "status" => $row['status'],
                        "delivery_status" => $row['delivery_status'],
                        "serviceID" => $row['service_id']
                    ];
                    $idsToUpdate[] = $row["id"];
                }

     
                $routingKey = 'topic.key';
                $numRows = count($dataList);

                // Publish messages to RabbitMQ
                try {
                    $channel->tx_select(); // Start RabbitMQ transaction

                    foreach ($dataList as $data) {
                        $msgData = json_encode($data);
                        // Log the processed message
                        error_log("INFO :: " . date("y-m-d H:i:s") . " [•] PHONE: " . $data['msisdn'] . "  MSG: " . substr($data['message'], 0, 4) . " SUBJECT: " . $data['email_subject'] . "\n\n", 3, $log);

                        $msg = new AMQPMessage($msgData, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
                        $channel->basic_publish($msg, $CHANNEL_NAME, $routingKey);
                        $count++;

                        // Batch update the status
                        error_log("INFO :: " . date("y-m-d H:i:s") . " [***] [•] Flagged ".$data['id']." as processed. [•] [***] \n", 3, $log);

                        $updateQuery = "UPDATE outgoing_bulk_emails SET status = 2 WHERE id = ". $data['id'];
                        DatabaseUtilities::executeQuery($mysqli, $updateQuery);

                        // Log the processed message
                        // error_log("INFO :: " . date("y-m-d H:i:s") . " [•] PHONE: " . $data['msisdn'] . "  MSG: " . substr($data['message'], 0, 5) . " SENDER_ID: " . $data['serviceID'] . " CLIENT: " . $data['apiClientID'] . "  ID: " . $data['id'] . "\n\n", 3, $log);
                    }

                    $channel->tx_commit(); // Commit RabbitMQ transaction
                    $mysqli->commit(); // Commit MySQL transaction

                    error_log("INFO :: " . date("y-m-d H:i:s") . " Processed " . $count . " logged requests\n", 3, $log);
                } catch (Exception $e) {
                    $channel->tx_rollback(); // Rollback RabbitMQ transaction
                    $mysqli->rollback(); // Rollback MySQL transaction

                    throw $e; // Re-throw the exception
                }
            } else {
                error_log("INFO :: " . date("y-m-d H:i:s") . " No records found \n", 3, $log);
            }

            $channel->close();
            error_log("INFO :: " . date("y-m-d H:i:s") . " CLOSED RabbitMQ channel::(" . $CHANNEL_NAME . ")\n", 3, $log);

            $connection->close();
            error_log("INFO :: " . date("y-m-d H:i:s") . " CLOSED RabbitMQ\n", 3, $log);

        } catch (Exception $e) {
            error_log($e->getMessage());
            echo "An error occurred. Please try again later.";
        }
    }
}

// $batchIDs = [];
//     foreach ($attibutes as $key => $value ) {
//         $batchIDs [] = [ $key => $value ];
// }

?>
