<?php
declare(strict_types=1);

require_once 'connection.php';
require_once 'User.php';
interface UserManagerInterface
{
    public function verifyUser(string $username, string $password): ?User;

    public function getAllUsers(): array;

    public function deleteUser(int $userId): bool;

    public function addUser(string $username, string $password): ?User;
}
class UserManager implements UserManagerInterface
{
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    public function verifyUser(string $username, string $password): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT user_id AS ID, username, password FROM users WHERE username = :username"
        );

        $stmt->execute([
            'username' => $username
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $user = User::fromArray($data);

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->query(
            "SELECT user_id AS ID, username FROM users"
        );

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = User::fromArray($row); 
        }

        return $users;
    }

    public function deleteUser(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM users WHERE user_id = :id"
        );

        return $stmt->execute([
            'id' => $userId
        ]);
    }

    public function addUser(string $username, string $password): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT user_id FROM users WHERE username = :username"
        );

        $stmt->execute([
            'username' => $username
        ]);

        if ($stmt->fetch()) {
            return null;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password) VALUES (:username, :password)"
        );

        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword
        ]);

        $id = (int)$this->db->lastInsertId();

        return new User($id, $username);
    }
}