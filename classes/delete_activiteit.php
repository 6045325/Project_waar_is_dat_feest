<?php
header('Content-Type: application/json');

require_once 'activiteitmanager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID is required']);
    exit;
}

$id = (int)$_POST['id'];
$manager = new ActiviteitenManager();

try {
    $success = $manager->deleteVacature($id);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Activiteit succesvol verwijderd']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Kon activiteit niet verwijderen']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
