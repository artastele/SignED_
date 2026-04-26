<?php

require_once '../app/helpers/Mailer.php';

$mailer = new Mailer();
$otp = rand(100000, 999999);

if ($mailer->sendOTP('allysacanonizado43@gmail.com', $otp)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}