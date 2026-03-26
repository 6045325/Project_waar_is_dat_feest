<?php
header('Content-Type: application/json');

require_once 'activiteitmanager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Controleer of alle vereiste velden aanwezig zijn
$required_fields = ['titel', 'beschrijving', 'datum', 'tijd', 'locatie', 'user_id'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Veld '$field' is verplicht"]);
        exit;
    }
}

try {
    $manager = new ActiviteitenManager();
    $imageUrl = trim($_POST['image_url'] ?? '');
    
    // Gebruik de bestaande addVacature functie
    $success = $manager->addVacature(
        trim($_POST['titel']),
        trim($_POST['beschrijving']),
        trim($_POST['datum']),
        trim($_POST['tijd']),
        trim($_POST['locatie']),
        $_POST['soort'] ?? 'Anders',
        $_POST['status'] ?? 'gepland',
        trim($_POST['opmerkingen'] ?? ''),
        (int)$_POST['user_id'],
        0.0, // lat - wordt automatisch opgehaald via getCoordinatesFromAddress
        0.0, // lng - wordt automatisch opgehaald via getCoordinatesFromAddress
        $imageUrl
    );
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Activiteit succesvol toegevoegd']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Kon activiteit niet toevoegen']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>