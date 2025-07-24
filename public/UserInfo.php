<?php
require "../class/User.php";
require_once "auth.php";
require_once "../logging/logByTP.php";

beginLog("userinfo");


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    endLog("no username", "userinfo");
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the username from session
$username = $_SESSION['username'];

try {
    error_log("session csrf token = " . $_SESSION['csrf_token']);

    $user = new User();
    $user->setUsername($username);

    $userController = new UserController($user);

    $user = $userController->getUserInfoFromDatabase();
    $_SESSION['user'] = $user;
    //$_SESSION['userController'] = $userController;

} catch (Exception $e) {
    logException("get user information", $e);
    endLog("error","userinfo");
    throw new Exception("error get user information:".$e->getMessage());
}

endLog("success", "userinfo");





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="../css/userinfo.css">
    <link rel="stylesheet" type="text/css" href="../css/editProfile.css">
    <link rel="stylesheet" type="text/css" href="../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../css/css3.css">
    <script type="text/javascript" src="../js/commonJS.js"></script>
    <script type="text/javascript" src="../js/UserInfoJS.js"></script>
    <?php
    include('view/editUserInfoView.php');
    include('view/commonView.html');
    ?>
</head>
<body>
    <div class="w-full">
        <!-- Header -->
        <div class="bg-white px-4 py-4 flex items-center justify-between border-b">
            <div class="flex items-center">
                <!-- ArrowLeft SVG -->
                <a href="Logout.php"><svg  xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600 mr-3"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>

                <div>
                    <div class="text-xs text-gray-400 uppercase tracking-wide">ACCOUNT</div>
                    <div class="text-gray-700 font-medium"><?php echo htmlspecialchars($user->getName() ?? ''); ?> · Owner</div>
                </div>
            </div>
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center text-sm font-medium" onclick="showEditModal()">
                <!-- Edit SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M12 5v14"></path>
                    <!-- Arrowhead (points upwards) -->
                    <path d="m5 12 7-7 7 7"></path></svg>
                Edit my account
            </button>
        </div>

        <!-- Profile Section -->
        <div class="bg-white mx-4 mt-6 rounded-lg p-6">
            <div class="flex items-start">
                <?php if (!empty($user->getProfilePicture())): ?>
                    <img src="<?php echo htmlspecialchars($user->getProfilePicture()); ?>" alt="Profile Picture" class="w-20 h-20 rounded-full object-cover mr-6">
                <?php else: ?>
                    <img src="../images/default-avatar.jpg" alt="Default Profile Picture" class="w-20 h-20 rounded-full object-cover mr-6">
                <?php endif; ?>

                <div class="flex-1">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($user->getName() ?? ''); ?></h2>
                    <p class="text-gray-500 mb-4">Owner</p>

                    <div class="space-y-3 text-sm">
                        <div class="flex">
                            <span class="text-gray-500 w-32 flex-shrink-0">Email address</span>
                            <span class="text-gray-900"><?php echo htmlspecialchars($user->getEmail()); ?></span>
                        </div>

                        <div class="flex">
                            <span class="text-gray-500 w-32 flex-shrink-0">Phone number</span>
                            <span class="text-gray-900"><?php echo htmlspecialchars($user->getPhoneNumber()); ?></span>
                        </div>

                        <div class="flex">
                            <span class="text-gray-500 w-32 flex-shrink-0">Direct managers</span>
                            <div class="flex-1">
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Phan Thanh Vũ 1</a>
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Nguyễn Huyền</a>
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Chu 23 Giang 1 chu thi</a>
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Ly Khoa</a>
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Bạch Hưng Kiên</a>
                                <a href="http://localhost:8080/public/userinfodemo2.html#" class="text-blue-500 hover:text-blue-600 block">Nguyễn Thái Bảo - Test</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr style="height: 50px;">
            <div class="flex items-start" style="width: 65%; padding: 20px;">
                <div class="flex-1">
                    <h3 class="text-gray-400 uppercase tracking-wide text-sm font-medium mb-4">CONTACT INFO</h3>

                    <div class="bg-white text-sm border-b border-t py-4 " >
                        <div class="flex">
                            <span class="text-gray-500 w-24 flex-shrink-0">Address</span>
                            <span class="text-gray-900"><?php echo htmlspecialchars($user->getAddress() ?? ''); ?></span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>
        displayError();
    </script>


</body>
</html>