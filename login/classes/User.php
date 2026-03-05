<?php

require_once 'connection.php';

class User extends Database {
    private string $username = " ";
    
    public function __construct() {
        parent::__construct();
    }

    public function verifyUser(string $username, string $password): ?array {
        $stmt = $this->getConnection()->prepare("SELECT ID, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch one row

        if ($user && password_verify($password, $user['password'])) {
            // Wachtwoord klopt, retourneer alle user data behalve het gehashte wachtwoord zelf
            unset($user['password']); // Verwijder het gehashte wachtwoord uit het array
            return $user;
        }
        return null;
    }
}
?>

