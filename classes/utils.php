<?php
require_once "PepipostEmailSender.php";
$api_key = "3d65cd3b2a075c359e9751336ec51af5";
$pepipostSender = new PepipostEmailSender($api_key);

$fromEmail = "admin.test@email.olivetreehub.com";
$fromName = "";
$toEmail = "kepha.okari@olivetreemobile.co";
$toName = "K Perk";
$subject = "copies Test PLEASE IGNORE";
$htmlContent = "<!DOCTYPE html>\n<html>\n<head>\n <title>HTML Email Example</title>\n</head>\n<body>\n    <h1>Hello!</h1>\n </p><p>[%LEAD%] & [%BAND%] will be singing a tribute song for our Heroes on Mashujaa Day   <p>Buy your ticket here.</p>\n    <a href=\"https://unsplash.com/photos/1gW7PlVTCOI\" target=\"_blank\">\n        <img src=\"https://images.unsplash.com/photo-1598612129114-b67489a20f33\" >\n    </a>\n    <a href=\"https://unsplash.com/photos/1gW7PlVTCOI\" target=\"_blank\" style=\"display:inline-block; padding:10px 20px; background-color:#007bff; color:#ffffff; text-decoration:none;\">Buy Ticket</a>\n</body>\n</html>";

$attachments = [
    ["name" => "SamplePDFFile_5mb.pdf", "content" => "base64_encoded_file_content"],
    ["name" => "SampleCSVFile_119kb.pdf", "content" => "base64_encoded_file_content"]
];
$attributes = [
    "LEAD" => "Khaligraph Jones",
    "BAND" => "Sauti Sol"
];
$bcc = [
    // ["email" => "kephaokari@gmail.com"],
    // ["email" => "kepha.okari@olivetreemobile.com"],
];
$cc = [
    ["email" => "rkapps47@gmail.com"],
    // ["email" => "kephaokari@gmail.com"],

];
// $schedule = time() + 3;
$schedule = null;


$result = $pepipostSender->sendEmail(
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

print_r($result);


