<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/UserManager.php';
ob_clean();

$userManager = new UserManager();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm = trim($_POST['confirm_password'] ?? '');

if ($username === '' || $email === '' || $password === '' || $confirm === '') {
    echo json_encode(['success' => false, 'message' => 'Vul alle velden in.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Vul een geldig e-mailadres in.']);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Wachtwoorden komen niet overeen.']);
    exit;
}

if ($userManager->addUser($username, $password, $email)) {
    echo json_encode(['success' => true, 'message' => 'Account succesvol aangemaakt! Je kunt nu inloggen.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gebruikersnaam of e-mailadres is al in gebruik.']);
}
?>
