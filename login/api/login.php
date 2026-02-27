<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
require_once __DIR__ . '/../classes/UserManager.php';
ob_clean();

$userManager = new UserManager();

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(['success'=>false,'message'=>'Vul alle velden in.']);
    exit;
}

$user = $userManager->verifyUser($username, $password);

if ($user) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'Gebruikersnaam of wachtwoord incorrect.']);
}