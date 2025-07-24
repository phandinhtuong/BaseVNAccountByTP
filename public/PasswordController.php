<?php

require_once '../schema/DataAccess.php';
require_once "../logging/logByTP.php";

class PasswordController
{
    private PasswordReset $passwordReset;
    private PDO $db;

    public function __construct(PasswordReset $passwordReset){
        $this->passwordReset = $passwordReset;
        $this->db = connectToDatabase();
    }

    public function addPasswordReset(): bool{

        try {
            $sql = "INSERT INTO password_resets (email, token, expires_at) 
                             VALUES (:email, :token, :expires_at)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $this->passwordReset->getEmail());
            $stmt->bindValue(':token', $this->passwordReset->getToken());
            $stmt->bindValue(':expires_at', $this->passwordReset->getExpiresAt());

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            logException("addPasswordReset", $e);
            throw $e;
        }
    }
    function authPasswordToken(): ?array {

        try {

            //$sql = "SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()";
            $sql = "SELECT email, expires_at FROM password_resets WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $this->passwordReset->getToken());

            $stmt->execute();

            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("reset email = ".$reset['email']);
            error_log("reset expires_at = ".$reset['expires_at']);

            if (!$reset) {
                return null;
            } else {
                return $reset;
            }

        } catch(PDOException $e) {
            logException("authPasswordToken", $e);
            throw $e;
        }
    }

    public function deletePasswordReset(): bool{

        try {
            $sql = "DELETE FROM password_resets WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $this->passwordReset->getToken());

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            logException("deletePasswordReset", $e);
            throw $e;
        }
    }

}