<?php
require_once __DIR__ . '/connection.php';

class ActiviteitManager extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function addActiviteit(
        string $titel,
        string $beschrijving,
        string $datum,
        string $tijd,
        string $locatie,
        string $soort,
        string $status,
        ?string $opmerkingen,
        int $userId
    ): bool {
        $stmt = $this->getConnection()->prepare(
            "INSERT INTO activiteiten (
                activiteit_titel,
                activiteit_beschrijving,
                activiteit_datum,
                activiteit_tijd,
                activiteit_locatie,
                soort_activiteit,
                activiteit_status,
                activiteit_opmerkingen,
                user_id
            ) VALUES (
                :titel,
                :beschrijving,
                :datum,
                :tijd,
                :locatie,
                :soort,
                :status,
                :opmerkingen,
                :user_id
            )"
        );

        return $stmt->execute([
            ':titel' => $titel,
            ':beschrijving' => $beschrijving,
            ':datum' => $datum,
            ':tijd' => $tijd,
            ':locatie' => $locatie,
            ':soort' => $soort,
            ':status' => $status,
            ':opmerkingen' => $opmerkingen ?: null,
            ':user_id' => $userId,
        ]);
    }

    public function getAllActiviteiten(): array {
        $stmt = $this->getConnection()->query(
            "SELECT * FROM activiteiten ORDER BY activiteit_datum ASC, activiteit_tijd ASC, activiteit_titel ASC"
        );
        return $stmt->fetchAll();
    }

    public function getActiviteitById(int $id): ?array {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM activiteiten WHERE activiteit_id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    public function updateActiviteit(
        int $id,
        string $titel,
        string $beschrijving,
        string $datum,
        string $tijd,
        string $locatie,
        string $soort,
        string $status,
        ?string $opmerkingen
    ): bool {
        $stmt = $this->getConnection()->prepare(
            "UPDATE activiteiten SET
                activiteit_titel = :titel,
                activiteit_beschrijving = :beschrijving,
                activiteit_datum = :datum,
                activiteit_tijd = :tijd,
                activiteit_locatie = :locatie,
                soort_activiteit = :soort,
                activiteit_status = :status,
                activiteit_opmerkingen = :opmerkingen
             WHERE activiteit_id = :id"
        );

        return $stmt->execute([
            ':id' => $id,
            ':titel' => $titel,
            ':beschrijving' => $beschrijving,
            ':datum' => $datum,
            ':tijd' => $tijd,
            ':locatie' => $locatie,
            ':soort' => $soort,
            ':status' => $status,
            ':opmerkingen' => $opmerkingen ?: null,
        ]);
    }

    public function deleteActiviteit(int $id): bool {
        $stmt = $this->getConnection()->prepare(
            "DELETE FROM activiteiten WHERE activiteit_id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
?>
