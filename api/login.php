<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../autoload.php';

$userManager = new UserManager();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$username || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Vul alle velden in."
    ]);
    exit;
}

$user = $userManager->verifyUser($username, $password);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Gebruikersnaam of wachtwoord incorrect."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => $user->toArray()
]);