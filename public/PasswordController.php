<?php

require_once '../schema/DataAccess.php';

class PasswordController
{
    private PDO $db;

    public function __construct(){
        $this->db = connectToDatabase();
    }

    public function addPasswordReset(string $email, string $token, string $expires_at): bool{

        try {
            $sql = "INSERT INTO password_resets (email, token, expires_at) 
                             VALUES (:email, :token, :expires_at)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':expires_at', $expires_at);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log("addPasswordReset failed: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }
    function authPasswordToken(string $token): ?array {

        try {

            //$sql = "SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()";
            $sql = "SELECT email, expires_at FROM password_resets WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $token);

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
            error_log("error authToken: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }

    public function deletePasswordReset(string $token): bool{

        try {
            $sql = "DELETE FROM password_resets WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $token);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log("deletePasswordReset failed: ".$e->getMessage() . ", at: ". $e->getTraceAsString());
            throw $e;
        }
    }

}