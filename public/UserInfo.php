<?php
require "../class/User.php";
require "UserController.php";

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html"); // Redirect to login page if not logged in
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

} catch (Exception $e) {
    error_log("error get user information: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
    throw new Exception("error get user information:".$e->getMessage());
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("post csrf token = " . $_POST['csrf_token'] . ", session csrf token = " . $_SESSION['csrf_token'].", CSRF token validation failed");
        die("CSRF token validation failed");
    }

    try {

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
            // Create a DateTime object
            //$dob = new DateTime("$year-$month-$day");
            $dob = "$year-$month-$day";

            //echo "<p><strong>Date of Birth:</strong> " . $dob->format('F j, Y') . "</p>";

        }

        $profilePictureBase64 = null;
//        if (isset($_FILES['profile_picture'])) {
//            error_log("profile pic ok 00");
//        } else{
//            error_log("profile pic oang 00");
//        }

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
//        else {
//            error_log("profile pic oang ");
//        }

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

//        $newData = [
//            'firstName' => $_POST['firstName'] ?? $user->getFirstName(),
//            'lastName' => $_POST['lastName'] ?? $user->getLastName(),
//            'name' => $_POST['firstName'] . " " . $_POST['lastName'] ?? $user->getName(),
//            'jobTitle' => $_POST['jobTitle'] ?? $user->getJobTitle(),
//            'companyName' => $_POST['companyName'] ?? $user->getCompanyName(),
//            'profile_picture' => $profilePictureBase64 ?? $user->getProfilePicture(),
//            'dob' => $dob ?? $user->getDob(),
//            'phoneNumber' => $_POST['phoneNumber'] ?? $user->getPhoneNumber(),
//            'address' => $_POST['address'] ?? $user->getAddress(),
//        ];


        if ($userController->update_profile($newUser)) {
            // Refresh user info
            $user = $userController->getUserInfoFromDatabase();
            echo '<script>alert("Profile updated successfully!");</script>';
        } else {
            echo '<script>alert("Error updating profile");</script>';
        }
    } catch (Exception $e) {
        error_log("error update profile: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
        echo '<script>alert("error update profile, see log for more details ");</script>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="cssForUserInfoDemo2_new.css">
    <link rel="stylesheet" type="text/css" href="../css/editProfile.css">

    <script>
        function showEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function hideEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                hideEditModal();
            }
        }
    </script>
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
<!--                <img src="../images/logo.full.png" alt="Profile" class="w-20 h-20 rounded-full object-cover mr-6">-->

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

            <!--        <div class="flex items-start">-->
            <!--            <h3 class="text-gray-400 uppercase tracking-wide text-sm font-medium mb-4">CONTACT INFO</h3>-->

            <!--        </div>-->
            <!--        <div class="flex items-start" >-->
            <!--            <div class="bg-white rounded-lg p-6 text-sm">-->
            <!--                <div class="flex">-->
            <!--                    <span class="text-gray-500 w-24 flex-shrink-0">Address</span>-->
            <!--                    <span class="text-gray-900">387 Vu Tong Phan Thanh Xuân, Hà Nội</span>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->

        </div>

    </div>




    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">


        <div style="background-color:white; margin:100px auto; width:80%; max-width:700px;">
            <div style="background-color:#EFEFEF; padding:10px;">

                <span style="float:right; cursor:pointer;" onclick="hideEditModal()">×</span>
                <h2>EDIT PERSONAL PROFILE</h2>
            </div>
            <form method="POST" enctype="multipart/form-data" style="padding:15px;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Your first name</div>
                        <div class="label-help">Your first name</div>
                    </div>
                    <div class="form-control">
                        <input type="text" placeholder="Your first name" name="firstName" value="<?php echo htmlspecialchars($user->getFirstName() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Your last name</div>
                        <div class="label-help">Your last name</div>
                    </div>
                    <div class="form-control">
                        <input type="text" placeholder="Your last name" name="lastName" value="<?php echo htmlspecialchars($user->getLastName() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Email</div>
                        <div class="label-help">Your email address</div>
                    </div>
                    <div class="form-control">
                        <input disabled type="email" placeholder="Your email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Username</div>
                        <div class="label-help">Your username</div>
                    </div>
                    <div class="form-control">
                        <input disabled type="text" placeholder="@username" name="username" value="<?php echo htmlspecialchars($user->getUsername() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Job title</div>
                        <div class="label-help">Job title</div>
                    </div>
                    <div class="form-control">
                        <input type="text" placeholder="Your job title" name="jobTitle" value="<?php echo htmlspecialchars($user->getJobTitle() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Profile image</div>
                        <div class="label-help">Profile image</div>
                    </div>
                    <div class="form-control">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" >
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Date of birth</div>
                        <div class="label-help">Date of birth</div>
                    </div>
                    <div class="form-control dob-selects">
                        <select id="day" name="day" required>
                            <option value="" selected disabled>Day</option>
                            <script>
                                const storedDay = <?php echo $user->getDob() ? (int)date('d', strtotime($user->getDob())) : 'null' ?>;
                                // Populate days (1-31)
                                for (let i = 1; i <= 31; i++) {
                                    const selected = storedDay === i ? ' selected' : '';
                                    document.write('<option value="' + i + '"' + selected + '>' + i + '</option>');
                                }
                            </script>
                        </select>

                        <!-- Month dropdown -->
                        <select id="month" name="month" required>
                            <option value="" disabled>Month</option>
                            <script>
                                // Get stored month from PHP
                                const storedMonth = <?php echo $user->getDob() !== null ? (int)date('m', strtotime($user->getDob())) : 'null' ?>;

                                // Month names array
                                const months = [
                                    [1, 'January'], [2, 'February'], [3, 'March'], [4, 'April'],
                                    [5, 'May'], [6, 'June'], [7, 'July'], [8, 'August'],
                                    [9, 'September'], [10, 'October'], [11, 'November'], [12, 'December']
                                ];

                                // Populate months
                                months.forEach(([num, name]) => {
                                    const selected = storedMonth === num ? ' selected' : '';
                                    document.write('<option value="' + num + '"' + selected + '>' + name + '</option>');
                                });
                            </script>
                        </select>

                        <!-- Year dropdown -->
                        <select id="year" name="year" required>
                            <option value="" disabled>Year</option>
                            <script>
                                // Get stored year from PHP
                                const storedYear = <?php echo $user->getDob() !== null ? (int)date('Y', strtotime($user->getDob())) : 'null' ?>;
                                const currentYear = new Date().getFullYear();

                                // Populate years (current year to 1900)
                                for (let i = currentYear; i >= 1900; i--) {
                                    const selected = storedYear === i ? ' selected' : '';
                                    document.write('<option value="' + i + '"' + selected + '>' + i + '</option>');
                                }
                            </script>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Your phone number</div>
                        <div class="label-help">Your phone number</div>
                    </div>
                    <div class="form-control">
                        <input type="text" placeholder="Phone number" name="phoneNumber" value="<?php echo htmlspecialchars($user->getPhoneNumber() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label">
                        <div class="label-title">Current address</div>
                        <div class="label-help">Current address</div>
                    </div>
                    <div class="form-control">
                        <input type="text" placeholder="Current address" name="address" value="<?php echo htmlspecialchars($user->getAddress() ?? ''); ?>">
                    </div>
                </div>
                <div style="border-top: 1px dashed #d1d5db; margin: 20px 0;"></div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="hideEditModal()">Cancel</button>
                    <button type="submit" class="btn-update" name="update_profile">Update</button>
                </div>
            </form>


        </div>
    </div>

</body>
</html>