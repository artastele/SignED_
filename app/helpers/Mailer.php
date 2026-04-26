<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';

class Mailer
{
    private function getMailer()
    {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'allysacanonizado43@gmail.com';
        $mail->Password = 'csyamuyqsetiwlwy';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        
        $mail->setFrom('allysacanonizado43@gmail.com', 'SignED SPED System');
        $mail->isHTML(true);
        
        return $mail;
    }

    public function sendOTP($toEmail, $otp)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = 'SignED OTP Verification';
            $mail->Body = "<h3>Your OTP is: $otp</h3>";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNotification($toEmail, $subject, $message, $isHtml = false)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = $subject;
            
            if ($isHtml) {
                $mail->Body = $message;
            } else {
                $mail->Body = nl2br(htmlspecialchars($message));
            }

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendEnrollmentNotification($toEmail, $learnerName, $status, $reason = null)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            
            if ($status === 'approved') {
                $mail->Subject = 'SPED Enrollment Approved - ' . $learnerName;
                $mail->Body = "
                    <h2>Enrollment Approved</h2>
                    <p>Dear Parent,</p>
                    <p>We are pleased to inform you that the SPED enrollment for <strong>{$learnerName}</strong> has been approved.</p>
                    <p>Your child has been successfully enrolled in our Special Education program. You will be contacted soon regarding the next steps in the assessment process.</p>
                    <p>Thank you for choosing our SPED program.</p>
                    <p>Best regards,<br>SignED SPED Team</p>
                ";
            } else {
                $mail->Subject = 'SPED Enrollment Status Update - ' . $learnerName;
                $mail->Body = "
                    <h2>Enrollment Status Update</h2>
                    <p>Dear Parent,</p>
                    <p>We regret to inform you that the SPED enrollment for <strong>{$learnerName}</strong> has been rejected.</p>
                    <p><strong>Reason for rejection:</strong><br>{$reason}</p>
                    <p>If you have any questions or would like to resubmit your application, please contact our SPED office.</p>
                    <p>Best regards,<br>SignED SPED Team</p>
                ";
            }

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendMeetingNotification($toEmail, $participantName, $learnerName, $meetingDate, $location)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = 'IEP Meeting Scheduled - ' . $learnerName;
            $mail->Body = "
                <h2>IEP Meeting Scheduled</h2>
                <p>Dear {$participantName},</p>
                <p>You have been invited to participate in an IEP meeting for <strong>{$learnerName}</strong>.</p>
                <p><strong>Meeting Details:</strong></p>
                <ul>
                    <li>Date & Time: " . date('F j, Y \a\t g:i A', strtotime($meetingDate)) . "</li>
                    <li>Location: {$location}</li>
                </ul>
                <p>Please confirm your attendance by logging into the system.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendIepApprovalNotification($toEmail, $recipientName, $learnerName)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = 'IEP Approved - ' . $learnerName;
            $mail->Body = "
                <h2>IEP Approved</h2>
                <p>Dear {$recipientName},</p>
                <p>The IEP for <strong>{$learnerName}</strong> has been approved by the Principal.</p>
                <p>The IEP is now active and implementation can begin.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendIepRejectionNotification($toEmail, $recipientName, $learnerName, $reason)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = 'IEP Requires Revision - ' . $learnerName;
            $mail->Body = "
                <h2>IEP Requires Revision</h2>
                <p>Dear {$recipientName},</p>
                <p>The IEP for <strong>{$learnerName}</strong> requires revision before approval.</p>
                <p><strong>Reason for revision:</strong><br>{$reason}</p>
                <p>Please review and resubmit the IEP after making the necessary changes.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }
}