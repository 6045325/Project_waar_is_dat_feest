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
        // Get participants if table exists, otherwise return empty array
        $participants = [];
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=Eventify;charset=utf8mb4", "root", "");
            $stmt = $pdo->prepare("
                SELECT p.* FROM participants p 
                WHERE p.activiteit_id = :activity_id
            ");
            $stmt->execute(['activity_id' => (int)$id]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Participants table doesn't exist yet, return empty array
            $participants = [];
        }
        
        echo json_encode([
            'success' => true, 
            'data' => $activiteit,
            'participants' => $participants
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Activiteit niet gevonden']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
