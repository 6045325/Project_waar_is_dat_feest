<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../classes/WeatherManager.php';

try {
    $weatherManager = new WeatherManager();
    
    // Controleer of latitude en longitude zijn meegegeven
    if (!isset($_GET['lat']) || !isset($_GET['lng'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Latitude en longitude zijn verplicht']);
        exit;
    }

    $latitude = (float)$_GET['lat'];
    $longitude = (float)$_GET['lng'];

    // Valideer coördinaten
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        http_response_code(400);
        echo json_encode(['error' => 'Ongeldige coördinaten']);
        exit;
    }

    $weather = $weatherManager->getWeather($latitude, $longitude);

    if ($weather === null) {
        http_response_code(503);
        echo json_encode(['error' => 'Kan weer data niet ophalen']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $weather
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>