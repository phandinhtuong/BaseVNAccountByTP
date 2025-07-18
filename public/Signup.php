<?php

session_start();

require "../class/User.php";
require "UserController.php";

try {
    if (isset($_POST['signup'])) {

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $user->setUsername($_POST['username']);
        $user->setName($_POST['name']);

        $userController = new UserController($user);


        //$errors = $userController->checkValidUserSignup();
        $result = $userController->checkValidUserSignup();

        //if (empty($errors)) {
        if ($result['success']) {

            if ($userController->signup()){
                header("Location: login.html?registration=success"."&email=".urlencode($_POST['email']));
                exit();
            };

        } else {
//            foreach ($errors as $error) {
//                echo "<p style='color:red;'>$error</p>";
//            }


            header("Location: signup.html?error=".$result['error']
                        ."&name=".urlencode($_POST['name'])
                        ."&username=".urlencode($_POST['username'])
                        ."&email=".urlencode($_POST['email']));
            exit();

        }

    } else {
        error_log("signup:  not found");
        echo 'Error, please try again later';
    }

} catch (PDOException $e) {
    error_log("Signup error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
    echo 'Error, please try again later';
}
