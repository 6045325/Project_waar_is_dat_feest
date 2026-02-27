nieuwe manager?


<?php
// MBOcinema/classes/UserManager.php

// Correct pad van 'classes' naar 'PROJECT'
require_once 'connection.php';

class UserManager extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Nieuwe methode om gebruiker te verifiëren
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
        return null; // Gebruiker niet gevonden of wachtwoord klopt niet
    }

    // Bestaande methodes...
    public function getAllUsers(): array {
        $stmt = $this->getConnection()->prepare("SELECT ID, Username FROM users");
        if ($stmt->execute()) {
            return $stmt->fetchAll();
        }
        return [];
    }

    public function deleteUser(int $userId): bool {
        if (!$userId) {
            return false;
        }
        $stmt = $this->getConnection()->prepare("DELETE FROM users WHERE ID = :ID");
        $stmt->bindParam(':ID', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Methode om een nieuwe gebruiker toe te voegen
    public function addUser(string $username, string $password): bool {
        // Controleer eerst of de gebruikersnaam al bestaat
        $stmt = $this->getConnection()->prepare("SELECT ID FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return false; // Gebruikersnaam bestaat al
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->getConnection()->prepare("INSERT INTO users (username, password) VALUES (:username, :password)"); // Standaard rol 'guest'
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        return $stmt->execute();
    }
}
?>

