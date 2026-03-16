<?php
require_once 'connection.php';

class UserManager extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function verifyUser(string $username, string $password): ?array {
        $stmt = $this->getConnection()->prepare("SELECT user_id, username, password, email FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public function addUser(string $username, string $password, string $email): bool {
        $check = $this->getConnection()->prepare("SELECT user_id FROM users WHERE username = :username OR email = :email LIMIT 1");
        $check->execute([':username' => $username, ':email' => $email]);
        if ($check->fetch()) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->getConnection()->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':email' => $email,
        ]);
    }
}
?>
