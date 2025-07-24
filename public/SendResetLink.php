<?php
require '../vendor/autoload.php'; // Include PHPMailer

require "../class/User.php";
require "../class/PasswordReset.php";
require "UserController.php";
require "PasswordController.php";
require_once "../logging/logByTP.php";

beginLog("send reset link");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists

    $user = new User();
    $user->setemail($email);

    $userController = new UserController($user);

    if ($userController->checkEmail()) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store in database
//        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
//        $stmt->execute([$email, $token, $expires]);
        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($email);
        $passwordReset->setToken($token);
        $passwordReset->setExpiresAt($expires);

        $passwordController = new PasswordController($passwordReset);
        $passwordController->addPasswordReset();

        // Send email
        $resetLink = "http://localhost:8080/public/ResetPassword.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click this link to reset your password: $resetLink\n\n";
        $message .= "This link will expire in 1 hour.";

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
        error_log("Location: Login.php?error=".$error."&email=".urlencode($_POST['email']));
        header("Location: Login.php?error=".$error."&email=".urlencode($_POST['email']));
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
    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'tanftanf01@gmail.com';
    $mail->Password = 'qfpn scub tnfs ptsx'; // Use app password for Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('tanftanf01@email.com', 'tan tan here');
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