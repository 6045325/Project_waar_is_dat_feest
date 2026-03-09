<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../autoload.php';

$userManager = new UserManager();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm = trim($_POST['confirm_password'] ?? '');

if (!$username || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Vul alle velden in."
    ]);
    exit;
}

if ($password !== $confirm) {
    echo json_encode([
        "success" => false,
        "message" => "Wachtwoorden komen niet overeen."
    ]);
    exit;
}

$user = $userManager->addUser($username, $password);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Gebruikersnaam bestaat al."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => $user->toArray()
]);