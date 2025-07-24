<?php
require '../vendor/autoload.php'; // Include PHPMailer

require "../class/User.php";
require "../class/PasswordReset.php";
require "UserController.php";
require "PasswordController.php";

error_log("----------------------------------------------------------------");
error_log("begin send reset link");

error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
error_log("POST VARIABLES:\n" . print_r($_POST, true));
error_log("COOKIE VARIABLES:\n" . print_r($_COOKIE, true));

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
                error_log("Location: ForgotPassword.php?error="
                    ."&email=".urlencode($_POST['email']));
                header("Location: ForgotPassword.php?error="
                    ."&email=".urlencode($_POST['email']));
                error_log("end send reset link");
                error_log("----------------------------------------------------------------\n");
                exit;
            }
        } catch (PDOException $e) {
            error_log("sendEmail error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
            error_log("Location: ForgotPassword.php?error="
                ."&email=".urlencode($_POST['email']));
            header("Location: ForgotPassword.php?error="
                ."&email=".urlencode($_POST['email']));
            error_log("end send reset link");
            error_log("----------------------------------------------------------------\n");
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
    error_log("end send reset link");
    error_log("----------------------------------------------------------------\n");
    exit;

}

function sendEmail($to, $subject, $message) : bool {
//    $headers = "From: no-reply@tplocalhost.com\r\n";
//    $headers .= "Reply-To: no-reply@tplocalhost.com\r\n";
//    $headers .= "MIME-Version: 1.0\r\n";
//    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
//
//    // For production, consider using PHPMailer or similar
//    return mail($to, $subject, $message, $headers);

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