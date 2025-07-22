<?php

require_once '../schema/DataAccess.php';

class UserController
{
    private User $user;
    private PDO $db;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->db = connectToDatabase();
    }

    public function processBeforeSignup()
    {
        $this->user->hashPassword();
        $this->user->setFirstNameAndLastName();
    }

    public function checkValidUserSignup(): array
    {
        try {
            //$errors = [];
            if (empty($this->user->getName())) {
                //$errors[] = "Username is required";
                //$errors[] = "nullUsername";
                $error = "nullName";
                return ['success' => false, 'error' => $error];
                //header("Location: signup.php?error=nullusername" . "&email=" . $user->getEmail(). "&name=" . $user->getName());
                //exit();
            }

            if (empty($this->user->getUsername())) {
                //$errors[] = "Username is required";
                //$errors[] = "nullUsername";
                $error = "nullUsername";
                return ['success' => false, 'error' => $error];
                //header("Location: signup.php?error=nullusername" . "&email=" . $user->getEmail(). "&name=" . $user->getName());
                //exit();
            }

            if (empty($this->user->getEmail())) {
                //$errors[] = "Email is required";
                //$errors[] = "nullEmail";
                $error = "nullEmail";
                return ['success' => false, 'error' => $error];
            } elseif (!filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                //$errors[] = "Invalid email format";
                //$errors[] = "invalidEmail";
                $error = "invalidEmail";
                return ['success' => false, 'error' => $error];
            }

            if (empty($this->user->getPassword())) {
                //$errors[] = "Password is required";
                //$errors[] = "nullPassword";
                $error = "nullPassword";
                return ['success' => false, 'error' => $error];
            } elseif (strlen($this->user->getPassword()) < 8) {
                //$errors[] = "Password must be at least 8 characters";
                //$errors[] = "weakPassword";
                $error = "weakPassword";
                return ['success' => false, 'error' => $error];
            }

            if ($this->userExists()){
                //$errors[] = "Username already exists";
                //$errors[] = "usernameExists";
                $error = "usernameExists";
                return ['success' => false, 'error' => $error];
            }

            //return $errors;
            return ['success' => true];

        } catch (PDOException $e){
            error_log("checkValidUserSignup error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
            throw $e;
        }

    }

    public function userExists(): bool{

        try {
            $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $this->user->getUsername());
            $stmt->bindValue(':email', $this->user->getEmail());
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                error_log("Username ".$this->user->getUsername()." or email ".$this->user->getEmail()." already exists.");
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDOException: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
            throw $e;
        }
    }
    public function signup(): bool{

        $this->processBeforeSignup();

        try {
            $sql = "INSERT INTO users (username, email, name, first_name, last_name, password) 
                             VALUES (:username, :email, :name, :first_name, :last_name, :password)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $this->user->getUsername());
            $stmt->bindValue(':email', $this->user->getEmail());
            $stmt->bindValue(':name', $this->user->getName());
            $stmt->bindValue(':first_name', $this->user->getFirstName());
            $stmt->bindValue(':last_name', $this->user->getLastName());
            $stmt->bindValue(':password', $this->user->getPassword());

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log("Registration failed: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }

    public function login(): array{
        //$errors = [];

        if (empty($this->user->getEmail()) || empty($this->user->getPassword())) {
            //header("Location: login.php?error=emptyfields");
            //exit();
            //$errors[] = "nullEmailOrPassword";
            $error = "nullEmailOrPassword";
            return ['success' => false, 'error' => $error];
        }

        // Check if user exists
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->user->getEmail());
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                //header("Location: login.php?error=nouser");
                //exit();
                //$errors[] = "noUser";
                $error = "noUser";
                return ['success' => false, 'error' => $error];
            }

            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (!password_verify($this->user->getPassword(), $dbUser['password'])) {
                //header("Location: login.php?error=wrongpwd");
                //exit();
                //$errors[] = "wrongPassword";
                $error = "wrongPassword";
                return ['success' => false, 'error' => $error];
            }

            // Login successful - start session
            //$_SESSION['user_id'] = $dbUser['id'];
            //$_SESSION['username'] = $dbUser['username'];

            $returnUser = new user();
            $returnUser->setId($dbUser['id']);
            $returnUser->setUsername($dbUser['username']);

            return ['success' => true, 'user' => $returnUser];

            // Redirect to dashboard or home page
            //header("Location: UserInfo.php");
            //exit();

        } catch (PDOException $e) {
            error_log("login failed: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }

    function getUserInfoFromDatabase(): User {

        try {

            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $this->user->getUsername());
            $stmt->execute();

            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userInfo) {
                throw new Exception("User not found");
            }

            $this->user->setId($userInfo['id']);
            $this->user->setEmail($userInfo['email']);
            $this->user->setName($userInfo['name']);
            $this->user->setFirstName($userInfo['first_name']);
            $this->user->setLastName($userInfo['last_name']);
            $this->user->setDob($userInfo['dob'] ?? '');
            $this->user->setCompanyName($userInfo['company_name'] ?? '');
            $this->user->setJobTitle($userInfo['job_title'] ?? '');
            $this->user->setPhoneNumber($userInfo['phone_number'] ?? '');
            $this->user->setManagers($userInfo['managers'] ?? '');
            $this->user->setAddress($userInfo['address'] ?? '');
            $this->user->setProfilePicture($userInfo['profile_picture'] ?? '');
            $this->user->setCreatedAt($userInfo['created_at']);

            return $this->user;

        } catch(PDOException $e) {
            error_log("getUserInfoFromDatabase error: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }

    function update_profile(User $newUser) {

        try {
            $sql = "
                UPDATE users SET
                    first_name    = :first_name, 
                    last_name     = :last_name, 
                    name          = :name, 
                    job_title     = :job_title, 
                    company_name  = :company_name, 
                    profile_picture = :profile_picture, 
                    dob           = :dob, 
                    phone_number  = :phone_number, 
                    address       = :address 
                WHERE 
                    username = :username
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':first_name', $newUser->getFirstName());
            $stmt->bindValue(':last_name', $newUser->getLastName());
            $stmt->bindValue(':name', $newUser->getName());
            $stmt->bindValue(':job_title', $newUser->getJobTitle());
            $stmt->bindValue(':company_name', $newUser->getCompanyName());
            $stmt->bindValue(':profile_picture', $newUser->getProfilePicture());
            $stmt->bindValue(':dob', $newUser->getDob());
            $stmt->bindValue(':phone_number', $newUser->getPhoneNumber());
            $stmt->bindValue(':address', $newUser->getAddress());
            $stmt->bindValue(':username', $this->user->getUsername());

            return $stmt->execute();

        } catch(PDOException $e) {
            error_log("error update_profile: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            return false;
        }
    }



}