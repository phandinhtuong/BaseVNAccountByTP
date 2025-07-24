<?php
require_once dirname(__DIR__, 2) . "/logging/logByTP.php";
require dirname(__DIR__, 2) . "/class/User.php";
require "UserController.php";
session_start();

beginLog("userinfo controller");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {


    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        endLog("post csrf token = " . $_POST['csrf_token'] . ", session csrf token = " . $_SESSION['csrf_token'].", CSRF token validation failed", "userinfo controller");
        die("CSRF token validation failed");
    }

    try {

        $user = $_SESSION['user'];
        $userController = new UserController($user);

        error_log("Files array: " . print_r($_FILES, true));
        error_log("Post data: " . print_r($_POST, true));

        $day = isset($_POST['day']) ? (int)$_POST['day'] : null;
        $month = isset($_POST['month']) ? (int)$_POST['month'] : null;
        $year = isset($_POST['year']) ? (int)$_POST['year'] : null;
        $dob = null;

        error_log("day = " . $day);
        error_log("month = " . $month);
        error_log("year = " . $year);

        if ($day && $month && $year && checkdate($month, $day, $year)) {
            $dob = "$year-$month-$day";
        }

        $profilePictureBase64 = null;

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            error_log("profile pic ok ");
            $file = $_FILES['profile_picture'];
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_types)) {
                $file_content = file_get_contents($file['tmp_name']);
                $profilePictureBase64 = 'data:image/' . $file_ext . ';base64,' . base64_encode($file_content);
            }
        }

        $newUser = new User();
        $newUser->setFirstName($_POST['firstName'] ?? $user->getFirstName());
        $newUser->setLastName($_POST['lastName'] ?? $user->getLastName());
        $newUser->setName($_POST['firstName'] . " " . $_POST['lastName'] ?? $user->getName());
        $newUser->setJobTitle($_POST['jobTitle'] ?? $user->getJobTitle());
        $newUser->setCompanyName($_POST['companyName'] ?? $user->getCompanyName());
        $newUser->setProfilePicture($profilePictureBase64 ?? $user->getProfilePicture());
        $newUser->setDob($dob ?? $user->getDob());
        $newUser->setPhoneNumber($_POST['phoneNumber'] ?? $user->getPhoneNumber());
        $newUser->setAddress($_POST['address'] ?? $user->getAddress());

        if ($userController->update_profile($newUser)) {
            // Refresh user info
            $user = $userController->getUserInfoFromDatabase();
            //echo '<script>alert("Profile updated successfully!");</script>';
            $error = "success";
            endLog("success", "userinfo controller");
            header("Location: ../UserInfo.php?error=".$error);
            exit();

        } else {
            //echo '<script>alert("Error updating profile");</script>';
            endLog("error", "userinfo controller");
            header("Location: ../UserInfo.php");
            exit();

        }
    } catch (Exception $e) {
        logException("update profile", $e);
        endLog("update profile error", "userinfo controller");
        //echo '<script>alert("error update profile, see log for more details ");</script>';
        endLog("error", "userinfo controller");
        header("Location: ../UserInfo.php");
        exit();
    }
}
