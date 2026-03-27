<?php

use BcMath\Number;

require_once "connection.php";

class ActiviteitenManager {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }


    /**
     * Haal coördinaten op via OpenStreetMap (Nominatim)
     */
    public function getCoordinatesFromAddress(string $address): ?array {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);

        $opts = [
            "http" => [
                "header" => "User-Agent: eventify/1.0\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!empty($data)) {
            return [
                "lat" => (float)$data[0]["lat"],
                "lng" => (float)$data[0]["lon"]
            ];
        }
        return null;
    }

    #C (Create)
    public function addVacature(
        string $activiteit_titel,
        string $activiteit_beschrijving,
        string $activiteit_datum,
        string $activiteit_tijd,
        string $activiteit_locatie,
        string $soort_activiteit,
        string $activiteit_status,
        string $activiteit_opmerkingen,
        int $user_id,
        float $lat,
        float $lng,
        string $activiteit_afbeelding_url = ''
    ): bool {

        // Eerst coördinaten ophalen
        $coords = $this->getCoordinatesFromAddress($activiteit_locatie);
        $lat = $coords['lat'] ?? null;
        $lng = $coords['lng'] ?? null;

        $stmt = $this->db->prepare("
            INSERT INTO activiteit (
                activiteit_titel,
                activiteit_beschrijving,
                activiteit_datum,
                activiteit_tijd,
                activiteit_locatie,
                soort_activiteit,
                activiteit_status,
                activiteit_opmerkingen,
                user_id,
                lat,
                lng,
                activiteit_afbeelding_url
            )
            VALUES (
                :activiteit_titel,
                :activiteit_beschrijving,
                :activiteit_datum,
                :activiteit_tijd,
                :activiteit_locatie,
                :soort_activiteit,
                :activiteit_status,
                :activiteit_opmerkingen,
                :user_id,
                :lat,
                :lng,
                :activiteit_afbeelding_url
            )
        ");

        $stmt->bindParam(':activiteit_titel', $activiteit_titel);        $stmt->bindParam(':activiteit_beschrijving', $activiteit_beschrijving);
        $stmt->bindParam(':activiteit_datum', $activiteit_datum);
        $stmt->bindParam(':activiteit_tijd', $activiteit_tijd);
        $stmt->bindParam(':activiteit_locatie', $activiteit_locatie);
        $stmt->bindParam(':soort_activiteit', $soort_activiteit);
        $stmt->bindParam(':activiteit_status', $activiteit_status);
        $stmt->bindParam(':activiteit_opmerkingen', $activiteit_opmerkingen);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':activiteit_afbeelding_url', $activiteit_afbeelding_url);

        return $stmt->execute();
    }

    #R (Read) - alle activiteiten
    public function getAllActiviteiten(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM activiteit ORDER BY `activiteit_titel` ASC
        ");
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    #R (Read) - enkele activiteit op ID
    public function getActiviteitById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM activiteit WHERE `activiteit_id` = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ?: null;
        }
        return null;
    }

    #R (Read) - activiteit binnen X km
    public function getActiviteitenWithinDistance(float $lat, float $lng, float $maxDistanceKm = 50): array {
        $sql = "
            SELECT *,
            (6371 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(lat)) *
                COS(RADIANS(lng) - RADIANS(:lng)) +
                SIN(RADIANS(:lat)) * SIN(RADIANS(lat))
            )) AS distance
            FROM activiteit
            HAVING distance <= :maxDistanceKm
            ORDER BY distance ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lat', $lat);
        $stmt->bindValue(':lng', $lng);
        $stmt->bindValue(':maxDistanceKm', $maxDistanceKm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActiviteitenSortedByDate() {
        $stmt = $this->db->prepare("SELECT * FROM activiteit ORDER BY activiteit_datum DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    #U (Update)
    public function updateActiviteit(
        int $activiteit_id,
        string $activiteit_titel,
        string $activiteit_beschrijving,
        string $activiteit_datum,
        string $activiteit_tijd,
        string $activiteit_locatie,
        string $soort_activiteit,
        string $activiteit_status,
        string $activiteit_opmerkingen,
        string $activiteit_afbeelding_url = ''
    ): bool {

        // Coördinaten opnieuw ophalen bij update
        $coords = $this->getCoordinatesFromAddress($activiteit_locatie);
        $lat = $coords['lat'] ?? null;
        $lng = $coords['lng'] ?? null;

        $stmt = $this->db->prepare("
            UPDATE activiteit SET 
                activiteit_titel = :activiteit_titel,
                activiteit_beschrijving = :activiteit_beschrijving,
                activiteit_datum = :activiteit_datum,
                activiteit_tijd = :activiteit_tijd,
                activiteit_locatie = :activiteit_locatie,
                soort_activiteit = :soort_activiteit,
                activiteit_status = :activiteit_status,
                activiteit_opmerkingen = :activiteit_opmerkingen,
                lat = :lat,
                lng = :lng,
                activiteit_afbeelding_url = :activiteit_afbeelding_url
            WHERE `activiteit_id` = :id
        ");

        $stmt->bindParam(':activiteit_titel', $activiteit_titel);
        $stmt->bindParam(':activiteit_beschrijving', $activiteit_beschrijving);
        $stmt->bindParam(':activiteit_datum', $activiteit_datum);
        $stmt->bindParam(':activiteit_tijd', $activiteit_tijd);
        $stmt->bindParam(':activiteit_locatie', $activiteit_locatie);
        $stmt->bindParam(':soort_activiteit', $soort_activiteit);
        $stmt->bindParam(':activiteit_status', $activiteit_status);
        $stmt->bindParam(':activiteit_opmerkingen', $activiteit_opmerkingen);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':activiteit_afbeelding_url', $activiteit_afbeelding_url);
        $stmt->bindParam(':id', $activiteit_id);

        return $stmt->execute();
    }

    #D (Delete)
    public function deleteVacature(int $id): bool {

        if (!$id) return false;

        $stmt = $this->db->prepare("DELETE FROM activiteit WHERE `activiteit_id` = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>