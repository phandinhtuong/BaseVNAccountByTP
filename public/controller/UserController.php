<?php

require_once dirname(__DIR__, 2) . "/schema/DataAccess.php";
require_once dirname(__DIR__, 2) . "/logging/logByTP.php";

class UserController
{
    private User $user;
    private PDO $db;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->db = connectToDatabase();
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function processBeforeSignup()
    {
        $this->user->hashPassword();
        $this->user->setFirstNameAndLastName();
    }

    public function checkValidUserSignup(): array
    {
        try {

            if (empty($this->user->getEmail())) {
                $error = "nullEmail";
                return ['success' => false, 'error' => $error];
            } elseif (!filter_var($this->user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $error = "invalidEmail";
                return ['success' => false, 'error' => $error];
            }

            if (empty($this->user->getUsername())) {
                $error = "nullUsername";
                return ['success' => false, 'error' => $error];
            }

            if (empty($this->user->getPassword())) {
                $error = "nullPassword";
                return ['success' => false, 'error' => $error];
            } elseif (strlen($this->user->getPassword()) < 8) {
                $error = "weakPassword";
                return ['success' => false, 'error' => $error];
            }

            if (empty($this->user->getName())) {
                $error = "nullName";
                return ['success' => false, 'error' => $error];
            }

            if ($this->userExists()){
                $error = "usernameExists";
                return ['success' => false, 'error' => $error];
            }

            return ['success' => true];

        } catch (PDOException $e){
            logException("checkValidUserSignup",$e);
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
            logException("userExists",$e);
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
            logException("signup",$e);
            throw $e;
        }
    }

    public function login(): array{
        if (empty($this->user->getEmail()) || empty($this->user->getPassword())) {
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
                $error = "noUser";
                return ['success' => false, 'error' => $error];
            }

            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (!password_verify($this->user->getPassword(), $dbUser['password'])) {
                $error = "wrongPassword";
                return ['success' => false, 'error' => $error];
            }

            $returnUser = new user();
            $returnUser->setId($dbUser['id']);
            $returnUser->setUsername($dbUser['username']);

            return ['success' => true, 'user' => $returnUser];

        } catch (PDOException $e) {
            logException("login",$e);
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
            logException("getUserInfoFromDatabase",$e);
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
            logException("update_profile",$e);
            return false;
        }
    }

    public function saveToken(): bool
    {
        try {
            $sql = "
                UPDATE users SET
                    login_token    = :login_token, 
                    login_token_expires     = :login_token_expires
                WHERE 
                    username = :username
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':login_token', $this->user->getLoginToken());
            $stmt->bindValue(':login_token_expires', $this->user->getLoginTokenExpires());
            $stmt->bindValue(':username', $this->user->getUsername());
            return $stmt->execute();

        } catch(PDOException $e) {
            logException("saveToken",$e);
            return false;
        }
    }

     function authToken(string $rememberToken): bool {

        try {

            $sql = "SELECT * FROM users WHERE username = :username and remember_token = :remember_token AND remember_expires > NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $this->user->getUsername());
            $stmt->bindValue(':remember_token', $rememberToken);

            $stmt->execute();

            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userInfo) {
                return false;
            } else {
                return true;
            }

        } catch(PDOException $e) {
            logException("authToken",$e);
            throw $e;
        }
    }

    public function clearToken(): bool
    {
        try {
            $sql = "
                UPDATE users SET
                    login_token    = null, 
                    login_token_expires     = null
                WHERE 
                    username = :username
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':username', $this->user->getUsername());
            return $stmt->execute();

        } catch(PDOException $e) {
            logException("clearToken",$e);
            return false;
        }
    }

    function checkEmail(): bool {

        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->user->getEmail());

            $stmt->execute();

            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userInfo) {
                return false;
            } else {
                return true;
            }

        } catch(PDOException $e) {
            logException("checkEmail",$e);
            throw $e;
        }
    }

    public function updatePassword()
    {
        try {
            $sql = "
                UPDATE users SET
                    password    = :password
                WHERE 
                    email = :email
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->user->getEmail());
            $stmt->bindValue(':password', $this->user->getPassword());
            return $stmt->execute();

        } catch(PDOException $e) {
            logException("updatePassword",$e);
            return false;
        }
    }


}