<?php

class PepipostEmailSender {
    private $api_key;
    private $base_url = "https://emailapi.netcorecloud.net/v5.1/mail/send";

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function sendEmail($fromEmail, $fromName=null, $toEmail, $toName=null, $subject, $htmlContent, $attributes = [], $attachments = [], $bcc = [], $cc = [], $schedule = null) {
        // Input validation
        if (empty($fromEmail) || empty($toEmail) || empty($subject) || empty($htmlContent)) {
            throw new InvalidArgumentException("Required parameters are missing.");
        }

        // Construct the data payload
        $data = [
            "from" => ["email" => $fromEmail, "name" => $fromName],
            "subject" => $subject,
            "template_id" => 0,
            "tags" => ["primary"],
            "content" => [
                ["type" => "html", "value" => $htmlContent]
            ],
            "personalizations" => [
                [
                    "attributes" => $attributes,
                    "to" => [["email" => $toEmail, "name" => $toName]],
                    "bcc" => empty($bcc) ? null : $bcc,
                    "cc" => empty($cc) ? null : $cc
                ]
            ],
            "attachments" => empty($attachments) ? null : $attachments,
            "settings" => [
                "open_track" => true,
                "click_track" => true,
                "unsubscribe_track" => true
            ],
            "schedule" => $schedule 
        ];

        // Make the API call and handle the response
        return $this->makeApiCall($data);
    }

    private function makeApiCall($data) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                "api_key: " . $this->api_key
            ]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new RuntimeException("cURL Error: " . $err);
        } else {
            return $response;
        }
    }
}
