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
    {
        error_log("INFO :: ".date("y-m-d H:i:s"). " About to get the unprocessed requests.\n", 3, $log);

        // Start a transaction
        $mysqli->begin_transaction();
        $SENDER = null;
        try {
            // Build the query to fetch unprocessed requests
            $query = "SELECT * FROM email_requests WHERE status = 1 LIMIT 5";
            $result = DatabaseUtilities::executeQuery($mysqli, $query);

            if (count($result) == 0) {

                error_log("INFO :: ".date("y-m-d H:i:s")." -- No record found -- \n\n", 3, $log);

            } else {

                error_log("INFO :: " . date("y-m-d H:i:s") . " -- [*] " . count($result) . " REQUESTS FOUND --\n", 3, $log);
            
                $insertValues = [];
                $cc =[];
                $bcc = [];

                foreach ($result as $row) {
                    $batchID = $row["id"];
                    $client_id = $row["client_id"];
                    $subject = $row["subject"];
                    $sender = $row["sender"];
                    $recipients = explode(",", $row["recipient"]);
                    $body = $row["body"];
                    // $secret_key = $row["secret_key"];
                    $sender_name = $row["sender_name"];
                    $recipient_name = $row["recipient_name"];
                    // $attachments = $row["attachments"];
                    // $attributes = $row["attributes"];
                    $attributes = json_decode($row["attributes"], true);
                    $cc = $row["cc"];
                    $bcc = $row["bcc"];

                    $schedule = $row["schedule"];
                    $status = $row["status"];
                    $unique_code = $row["unique_code"];
                    
                    foreach ($recipients as $to_email) {
                        $api_key = "3d65cd3b2a075c359e9751336ec51af5";
                        $pepipostSender = new PepipostEmailSender($api_key);

                        $fromEmail = $sender;
                        $fromName = $sender_name;
                        $toEmail = $to_email;
                        $toName = $recipient_name;
                        // $subject = "copies Test PLEASE IGNORE";
                        $htmlContent = $body;

                        $attachments = [
                            // ["name" => "SamplePDFFile_5mb.pdf", "content" => "base64_encoded_file_content"],
                            // ["name" => "SampleCSVFile_119kb.pdf", "content" => "base64_encoded_file_content"]
                        ];
                        
                        // $attributes = [
                        //     "LEAD" => "Khaligraph Jones",
                        //     "BAND" => "Sauti Sol"
                        // ];

                        $cc = [
                            // ["email" => "rkapps47@gmail.com"],
                            // ["email" => "kephaokari@gmail.com"],
                        ];

                        if (!empty($row["cc"])) {
                            foreach (explode(',', $row["cc"]) as $cc_email) {
                                $cc[] = ["email" => trim($cc_email)];
                            }
                        }
                        
                        $bcc = [
                            // ["email" => "kephaokari@gmail.com"],
                            // ["email" => "kepha.okari@olivetreemobile.com"],
                        ];

                        if (!empty($row["bcc"])) {
                            foreach (explode(',', $row["bcc"]) as $bcc_email) {
                                $bcc[] = ["email" => trim($bcc_email)];
                            }
                        }
               
                        // $schedule = time() + 3;
                        // $schedule = null;

                        # check for email credits
                        $queryEmailCredits = "SELECT email_credits_cr, email_credits_dr, email_credits FROM clients WHERE id = ". $client_id;
                        $emailCredits = DatabaseUtilities::executeQuery($mysqli, $queryEmailCredits);
                        foreach ($emailCredits as $row) {
                            // Customize this part to format your data as needed
                            $email_credits_cr=$row["email_credits_cr"];
                            $email_credits_dr=$row["email_credits_dr"];
                            $email_credits=$row["email_credits"];
                            $output = "CR: {$email_credits_cr}, DR: {$email_credits_dr}, Credits: {$email_credits}\n";
                        }
                        
                        $mergedArray = array_merge($cc, $bcc);
                        $totalCount = count($recipients) + count($mergedArray);

                        if($email_credits >= $totalCount){

                                                 
                            $debitCreditsQuery = "UPDATE clients
                                SET email_credits_dr = email_credits_dr + $totalCount,
                                email_credits = email_credits - $totalCount
                                WHERE id = $client_id";
                            DatabaseUtilities::executeQuery($mysqli, $debitCreditsQuery);
    
                            error_log("INFO :: " . date("y-m-d H:i:s") . " -- processed Response--\n".$totalCount ." EMAILS BILLED\n", 3, $log);


                            $emailResp = $pepipostSender->sendEmail(
                                $fromEmail,
                                $fromName,
                                $toEmail,
                                $toName,
                                $subject,
                                $htmlContent,
                                $attributes,
                                $attachments,
                                $bcc,
                                $cc,
                                $schedule
                            );

                            error_log("INFO :: " . date("y-m-d H:i:s") . " -- processed Response--\n".$emailResp, 3, $log);
                            # Handle the Billing
                            $responseData = json_decode($emailResp, true);
                            $status_message = "processed";
                            $updateQuery = "UPDATE email_requests SET provider_response = '".$emailResp."', status_message = '".$status_message."', send_attempts =  send_attempts + 1 WHERE id = ".$batchID;
                            DatabaseUtilities::executeQuery($mysqli, $updateQuery);
                            
                            error_log("INFO :: " . date("y-m-d H:i:s") . " -- STATUS MESSAGE: ".$status_message." \n", 3, $log);

       
                        }else {
                            # insufficient balance
                            $status_message = "Insufficient email credits";
                            $updateQuery = "UPDATE email_requests 
                                SET status_message = '" . $status_message . "' 
                                WHERE id = " . $batchID;
                            DatabaseUtilities::executeQuery($mysqli, $updateQuery);
                            error_log("INFO :: " . date("y-m-d H:i:s") . " -- STATUS MESSAGE: ".$status_message." \n", 3, $log);

                            $mysqli->commit();
                        }

                        $updateQuery = "UPDATE email_requests SET status = 2 WHERE id = ".$batchID;
                        DatabaseUtilities::executeQuery($mysqli, $updateQuery);
                        $mysqli->commit();

                        error_log("INFO :: " . date("y-m-d H:i:s") . "Requests marked as processed\n", 3, $log);


                    }
                }
            
            }
            
        } catch (Exception $e) {
            // Rollback the transaction in case of an exception
            error_log("ERROR :: ".date("y-m-d H:i:s")." -- Exception occurred: ".$e->getMessage()."\n", 3, $log);
        }
    }
}

// $batchIDs = [];
//     foreach ($attibutes as $key => $value ) {
//         $batchIDs [] = [ $key => $value ];
// }

?>
