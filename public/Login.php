<?php

require "../class/User.php";
require "UserController.php";

session_start();

try {
    if (isset($_POST['login-submit'])) {

        //$db = connectToDatabase();

        // Get form data
        //$userInput = trim($_POST['email']);
        //$password = $_POST['password'];

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);

        $userController = new UserController($user);


        // Validate inputs
        //if (empty($userInput) || empty($password)) {
        //    header("Location: login.html?error=emptyfields");
        //    exit();
        //}
        $result = $userController->login();

        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']->getId();
            $_SESSION['username'] = $result['user']->getUsername();
            header("Location: UserInfo.php");
            exit();
        } else {
//            foreach ($result['errors'] as $error) {
//                echo "<p style='color:red;'>$error</p>";
//            }
            error_log("Login error: email: ".$_POST['email'] . ", error:" . $result['error']);

            header("Location: login.html?error=".$result['error']
                ."&email=".urlencode($_POST['email']));
            exit();
        }

    } else {
        header("Location: login.html");
        exit();
    }
} catch (PDOException $e) {
    error_log("Login error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
    echo 'Error, please try again later';
}
