<?php
header('Content-Type: application/json');

require_once 'activiteitmanager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

try {
    $manager = new ActiviteitenManager();
    $activiteit = $manager->getActiviteitById((int)$id);
    
    if ($activiteit) {
        echo json_encode(['success' => true, 'data' => $activiteit]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Activiteit niet gevonden']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>