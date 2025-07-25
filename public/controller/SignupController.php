<?php
session_start();

require dirname(__DIR__, 2) . "/class/User.php";
require dirname(__DIR__, 1) . "/service/UserService.php";
require_once dirname(__DIR__, 2) . "/logging/logByTP.php";

beginLog("signup controller");

try {
    if (isset($_POST['signup'])) {

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $user->setUsername($_POST['username']);
        $user->setName($_POST['name']);

        $userService = new UserService($user);

        $result = $userService->checkValidUserSignup();

        if ($result['success']) {

            if ($userService->signup()){
                endLog("success", "signup");
                header("Location: ../Login.php?registration=success"."&email=".urlencode($_POST['email']));
                exit();
            };

        } else {
            endLog("Location: ../Signup.php?error=".$result['error']
                ."&name=".urlencode($_POST['name'])
                ."&username=".urlencode($_POST['username'])
                ."&email=".urlencode($_POST['email']), "signup controller");

            header("Location: ../Signup.php?error=".$result['error']
                ."&name=".urlencode($_POST['name'])
                ."&username=".urlencode($_POST['username'])
                ."&email=".urlencode($_POST['email']));
            exit();
        }
    }
} catch (PDOException $e) {
    logException("signup", $e);
    endLog("error", "signup controller");
    echo 'Error, please try again later';
}