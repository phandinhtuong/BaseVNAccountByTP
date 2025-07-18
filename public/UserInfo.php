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
    <title>User Information</title>
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
    <div class="container">
        <h1>User Information</h1>

        <div class="user-info">
            <h2>Welcome, <?php echo htmlspecialchars($user->getUsername()); ?>!</h2>

            <h3>Account Details</h3>
            <?php if (!empty($user->getProfilePicture())): ?>
                <img src="<?php echo htmlspecialchars($user->getProfilePicture()); ?>" alt="Profile Picture" style="max-width: 200px;">
            <?php endif; ?>
            <ul>

                <li><strong>Name:</strong> <?php echo htmlspecialchars($user->getName() ?? ''); ?></li>
                <li><strong>Username:</strong> <?php echo htmlspecialchars($user->getUsername() ?? ''); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></li>
                <li><strong>DOB:</strong> <?php echo date('d/m/Y', strtotime($user->getDob())); ?></li>
                <li><strong>Address:</strong> <?php echo htmlspecialchars($user->getAddress() ?? ''); ?></li>
                <li><strong>Registration Date:</strong> <?php echo date('d/m/Y', strtotime($user->getCreatedAt())); ?></li>
            </ul>
        </div>
        <button onclick="showEditModal()">Edit Profile</button>
    </div>

    <a href="Logout.php" class="logout-btn">Logout</a>

    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
        <div style="background-color:white; margin:100px auto; padding:20px; width:80%; max-width:500px;">
            <span style="float:right; cursor:pointer;" onclick="hideEditModal()">×</span>
            <h2>Edit Profile</h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Your first name</label>
                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($user->getFirstName() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Your last name</label>
                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($user->getLastName() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Email:</label>
                    <input disabled type="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Username</label>
                    <input disabled type="text" name="username" value="<?php echo htmlspecialchars($user->getUsername() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Job title</label>
                    <input type="text" name="jobTitle" value="<?php echo htmlspecialchars($user->getJobTitle() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Company name</label>
                    <input type="text" name="companyName" value="<?php echo htmlspecialchars($user->getCompanyName() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Profile Picture</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display:none;"
                               onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'No file chosen'">
                        <button type="button" onclick="document.getElementById('profile_picture').click()">Choose File</button>
                        <span id="file-name">No file chosen</span>
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Date of birth</label>
                    <div class="dob-container">
                        <!-- Day dropdown -->
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



                <div style="margin-bottom:15px;">
                    <label style="display:block;">Your phone number</label>
                    <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($user->getPhoneNumber() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block;">Current address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($user->getAddress() ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div style="text-align:center;">
                    <button type="button" onclick="hideEditModal()">Cancel</button>
                    <button type="submit" name="update_profile">Update</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>