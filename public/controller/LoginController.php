<?php
require_once dirname(__DIR__, 2) . "/logging/logByTP.php";
require dirname(__DIR__, 2) . "/class/User.php";
require "UserController.php";
require_once dirname(__DIR__, 2) . '/schema/Config.php';

session_start();
beginLog("login controller");

//generate login token
function generateToken(): string
{
    try {
        return bin2hex(random_bytes(32));
    } catch (Exception $e) {
        logException("generateToken", $e);
        throw $e;
    }
}

try {
    if (isset($_POST['login-submit'])) {

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $show_captcha = $_SESSION['failed_attempts'] >= numberOfFailedLoginsToShowCaptcha;
        $remember = isset($_POST['remember']);
        $remembered = $_POST['remembered'] ?? "";

        // CAPTCHA verification if needed
        if ($show_captcha) {
            if (empty($_POST['captcha']) || empty($_SESSION['captcha']) ||
                strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha'])) {
                $error = "invalidCAPTCHA";
                $_SESSION['failed_attempts']++;
            }
        }

        if (!isset($error)) {
            $userController = new UserController($user);

            $result = $userController->login();

            if ($result['success']) {
                $userController->setUser($result['user']);
                $_SESSION['user_id'] = $result['user']->getId();
                $_SESSION['username'] = $result['user']->getUsername();
                unset($_SESSION['failed_attempts']);
                unset($_SESSION['captcha']);

                if ($remember) {
                    $token = generateToken();
                    $expires = date('Y-m-d H:i:s', strtotime('+'.numberOfDaysRemainingLogins.' days'));

                    $userController->getUser()->setLoginToken($token);
                    $userController->getUser()->setLoginTokenExpires($expires);

                    if ($userController->saveToken()) {
                        error_log("user ". $result['user']->getUsername() . " save token successfully: ".$token . ", expires " . $expires);
                        // Set cookie (secure, HttpOnly, SameSite)
                        setcookie(
                            'remember',
                            $_SESSION['username'] . ':' . $token,
                            [
                                'expires' => strtotime('+'.numberOfDaysRemainingLogins.' days'),
                                'path' => '/',
                                'secure' => true, // HTTPS only
                                'httponly' => true,
                                'samesite' => 'Strict'
                            ]
                        );

                    } else {
                        endLog("user ". $result['user']->getUsername() . "save token error", "login controller");
                        header("Location: ../Login.php?error=".$result['error']
                            ."&email=".urlencode($_POST['email'])."&remember=".urlencode($remembered));
                        exit();
                    }
                }
                endLog("login success", "login controller");
                header("Location: ../UserInfo.php");
                exit();
            } else {
                $_SESSION['failed_attempts']++;
                endLog("Login error: email: ".$_POST['email'] . ", error:" . $result['error'], "login controller");
                header("Location: ../Login.php?error=".$result['error']
                    ."&email=".urlencode($_POST['email'])."&remember=".urlencode($remembered));
                exit();
            }
        } else {
            endLog("Login error: email: ".$_POST['email'] . ", error:" . $error, "login controller");
            header("Location: ../Login.php?error=".$error
                ."&email=".urlencode($_POST['email'])."&remember=".urlencode($remembered));
            exit();
        }
    }
} catch (PDOException $e) {
    logException("login controller", $e);
    echo 'Error, please try again later';
    endLog("Error", "login controller");
}
endLog("success","login controller");
