<?php

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $name;
    private string $firstName;
    private string $lastName;
    private string $password;
    private string $dob;
    private string $companyName;
    private string $jobTitle;
    private string $phoneNumber;
    private string $managers;
    private string $address;
    private string $profile_picture;
    private string $user_id;
    private string $system_id;
    private string $login_token;
    private string $login_token_expires;
    private string $created_at;

    public function __construct()
    {

    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getDob(): string
    {
        return $this->dob;
    }

    /**
     * @param string $dob
     */
    public function setDob(string $dob): void
    {
        $this->dob = $dob;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     */
    public function setJobTitle(string $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getManagers(): string
    {
        return $this->managers;
    }

    /**
     * @param string $managers
     */
    public function setManagers(string $managers): void
    {
        $this->managers = $managers;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getProfilePicture(): string
    {
        return $this->profile_picture;
    }

    /**
     * @param string $profile_picture
     */
    public function setProfilePicture(string $profile_picture): void
    {
        $this->profile_picture = $profile_picture;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     */
    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getSystemId(): string
    {
        return $this->system_id;
    }

    /**
     * @param string $system_id
     */
    public function setSystemId(string $system_id): void
    {
        $this->system_id = $system_id;
    }

    /**
     * @return string
     */
    public function getLoginToken(): string
    {
        return $this->login_token;
    }

    /**
     * @param string $login_token
     */
    public function setLoginToken(string $login_token): void
    {
        $this->login_token = $login_token;
    }

    /**
     * @return string
     */
    public function getLoginTokenExpires(): string
    {
        return $this->login_token_expires;
    }

    /**
     * @param string $login_token_expires
     */
    public function setLoginTokenExpires(string $login_token_expires): void
    {
        $this->login_token_expires = $login_token_expires;
    }


    public function hashPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }
    public function setFirstNameAndLastName(){
        $fullName = trim($this->name);

        $firstName = '';
        $lastName = '';

        $nameParts = preg_split('/\s+/', $fullName);
        $count = count($nameParts);

        if ($count === 1) {
            // Only one name provided
            $firstName = $nameParts[0];
        } elseif ($count === 2) {
            // Simple first name + last name
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];
        } elseif ($count > 2) {
            // Handle multiple last names or middle names
            $firstName = $nameParts[0];
            $lastName = implode(' ', array_slice($nameParts, 1));
        }

        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

}