<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../classes/UserManager.php';
ob_clean();
$userManager = new UserManager();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm = trim($_POST['confirm_password'] ?? '');

if ($password !== $confirm) {
    echo json_encode([
        "success" => false,
        "message" => "Wachtwoorden komen niet overeen."
    ]);
    exit;
}

if ($userManager->addUser($username, $password)) {
    echo json_encode([
        "success" => true,
        "message" => "Account succesvol aangemaakt! Je kunt nu inloggen."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gebruikersnaam is al in gebruik."
    ]);
}