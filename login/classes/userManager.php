<?php

require_once 'connection.php';
require_once 'User.php';

class UserManager extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function verifyUser(string $username, string $password): ?User
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT ID, username, password FROM users WHERE username = :username"
        );

        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && password_verify($password, $data['password'])) {
            return new User($data['ID'], $data['username']);
        }

        return null;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT ID, username FROM users"
        );

        $stmt->execute();

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['ID'], $row['username']);
        }

        return $users;
    }

    public function deleteUser(int $userId): bool
    {
        $stmt = $this->getConnection()->prepare(
            "DELETE FROM users WHERE ID = :id"
        );

        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function addUser(string $username, string $password): bool
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT ID FROM users WHERE username = :username"
        );

        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->getConnection()->prepare(
            "INSERT INTO users (username, password) VALUES (:username, :password)"
        );

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        return $stmt->execute();
    }
}