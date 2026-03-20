<?php
declare(strict_types=1);

class User
{
    private int $id;
    private string $username;
    private string $password;

    public function __construct(int $id = 0, string $username = "", string $password = "")
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public static function fromArray(array $data): User
    {
        return new User(
            (int)$data['ID'],
            $data['username'],
            $data['password'] ?? ""
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "username" => $this->username
        ];
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

}