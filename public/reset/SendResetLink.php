<?php
require '../../vendor/autoload.php'; // Include PHPMailer

require "../../class/User.php";
require "../../class/PasswordReset.php";
require "../service/UserService.php";
require "../service/PasswordResetService.php";
require_once "../../logging/logByTP.php";
require_once "../../schema/Config.php";

beginLog("send reset link");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists

    $user = new User();
    $user->setemail($email);

    $userService = new UserService($user);

    if ($userService->checkEmail()) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+'.numberOfHoursResetTokenExpires.' hour'));

        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($email);
        $passwordReset->setToken($token);
        $passwordReset->setExpiresAt($expires);

        $passwordResetService = new PasswordResetService($passwordReset);
        $passwordResetService->addPasswordReset();

        // Send email
        $resetLink = linkWebsite . "reset/ResetPassword.php?token=$token";
        $subject = resetPasswordMailSubject;
        $message = "Click this link to reset your password: $resetLink\n\n";
        $message .= "This link will expire in ".numberOfHoursResetTokenExpires." hour(s).";

        try {
            if (!sendEmail($email, $subject, $message)) {
                endLog("Location: ForgotPassword.php?error="
                    ."&email=".urlencode($_POST['email']),"send reset link");
                header("Location: ForgotPassword.php?error="
                    ."&email=".urlencode($_POST['email']));
                exit;
            }
        } catch (PDOException $e) {
            logException("sendEmail", $e);
            endLog("Location: ForgotPassword.php?error="
                ."&email=".urlencode($_POST['email']),"send reset link");
            header("Location: ForgotPassword.php?error="
                ."&email=".urlencode($_POST['email']));
            exit;
        }

        $error = "resetLinkSent";
        error_log("Location: ../Login.php?error=".$error."&email=".urlencode($_POST['email']));
        header("Location: ../Login.php?error=".$error."&email=".urlencode($_POST['email']));
    } else {
        $error = "emailNotFound";
        error_log("Location: ForgotPassword.php?error=".$error
            ."&email=".urlencode($_POST['email']));
        header("Location: ForgotPassword.php?error=".$error
            ."&email=".urlencode($_POST['email']));
    }

    endLog("success", "send reset link");
    exit;

}

function sendEmail($to, $subject, $message) : bool {

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = mailHost;
    $mail->SMTPAuth = true;
    $mail->Username = mailSenderUsername;
    $mail->Password = mailSenderAppPassword; // app password for Gmail
    $mail->SMTPSecure = mailSMTPSecure;
    $mail->Port = mailPort;

    $mail->setFrom(mailSenderUsername, mailSenderName);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;

    if(!$mail->send()) {
        error_log("Message could not be sent");
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    } else {
        error_log("Message has been sent");
        return true;
    }


}