
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'classes/activiteitmanager.php';
require_once 'classes/userManager.php';

$manager = new ActiviteitenManager();

// Bepaal sortering
$sortBy = $_GET['sort'] ?? 'titel';
if ($sortBy === 'datum') {
    $activiteiten = $manager->getAllActiviteitenSortedByDate();
} else {
    $activiteiten = $manager->getAllActiviteiten();
}

// Bepaal filteren op afstand (standaard 50km)
$filterDistance = isset($_GET['distance']) ? (float)$_GET['distance'] : null;
if ($filterDistance !== null && $filterDistance > 0) {
    // Voor afstandsberekening, gebruiken we Amsterdam als standaard (52.3676, 4.9041)
    // In een echte applicatie zou je de locatie van de ingelogde gebruiker gebruiken
    $userLat = $_GET['lat'] ?? 52.3676;
    $userLng = $_GET['lng'] ?? 4.9041;
    $activiteiten = $manager->getActiviteitenWithinDistance((float)$userLat, (float)$userLng, $filterDistance);
}

// Bepaal zoekterm
$searchTerm = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Activiteiten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div id="Activiteiten-page">
    <?php require_once 'includes/navbar.php'; ?>
</div>

<div class="container">
    <h1>Activiteiten</h1>

    <!-- Filter & Zoek Controls -->
    <div class="activiteiten-controls">
        <form method="GET" class="controls-form">
            <!-- Zoeken -->
            <div class="control-group">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Zoek op titel, beschrijving of locatie..." 
                    value="<?= htmlspecialchars($searchTerm) ?>"
                    id="searchInput"
                    class="search-input"
                >
            </div>

            <!-- Sorteren -->
            <div class="control-group">
                <label for="sortSelect">Sorteren op:</label>
                <select name="sort" id="sortSelect" class="sort-select">
                    <option value="titel" <?= ($sortBy === 'titel') ? 'selected' : '' ?>>Titel (A-Z)</option>
                    <option value="datum" <?= ($sortBy === 'datum') ? 'selected' : '' ?>>Datum (Nieuwste eerst)</option>
                </select>
            </div>

            <!-- Afstand Filter -->
            <div class="control-group">
                <label for="distanceSelect">Afstand:</label>
                <select name="distance" id="distanceSelect" class="distance-select" onchange="this.form.submit()">
                    <option value="" <?= ($filterDistance === null) ? 'selected' : '' ?>>Alle activiteiten</option>
                    <option value="10" <?= ($filterDistance === 10) ? 'selected' : '' ?>>Tot 10 km</option>
                    <option value="25" <?= ($filterDistance === 25) ? 'selected' : '' ?>>Tot 25 km</option>
                    <option value="50" <?= ($filterDistance === 50) ? 'selected' : '' ?>>Tot 50 km</option>
                    <option value="100" <?= ($filterDistance === 100) ? 'selected' : '' ?>>Tot 100 km</option>
                </select>
            </div>

            <button type="submit" class="btn-search">Zoeken</button>
            <a href="Activiteiten.php" class="btn-reset">Alles wissen</a>
        </form>
    </div>

    <!-- Resultaten -->
    <div class="results-info">
        <p id="resultsCount">
            <?php 
            $filteredCount = count($activiteiten);
            if ($searchTerm) {
                echo "Zoekresultaten voor '<strong>" . htmlspecialchars($searchTerm) . "</strong>': " . $filteredCount . " gevonden";
            } else if ($filterDistance) {
                echo "Activiteiten binnen <strong>" . $filterDistance . " km</strong>: " . $filteredCount . " gevonden";
            } else {
                echo "Totaal: <strong>" . $filteredCount . "</strong> activiteiten";
            }
            ?>
        </p>
    </div>

    <div id="activiteiten-lijst" class="cards">
        <?php 
        if (empty($activiteiten)): 
        ?>
            <div class="no-results">
                <p>Geen activiteiten gevonden</p>
            </div>
        <?php 
        else:
            foreach ($activiteiten as $a): 
                // Zoekfilter toepassen
                if ($searchTerm) {
                    $titel = strtolower($a['activiteit_titel']);
                    $beschrijving = strtolower($a['activiteit_beschrijving']);
                    $locatie = strtolower($a['activiteit_locatie']);
                    $search = strtolower($searchTerm);
                    
                    if (!str_contains($titel, $search) && 
                        !str_contains($beschrijving, $search) && 
                        !str_contains($locatie, $search)) {
                        continue;
                    }
                }
        ?>
            <div class="card">
                <div class="card-header">
                    <h2><?= htmlspecialchars($a['activiteit_titel']) ?></h2>
                    <span class="status <?= htmlspecialchars($a['activiteit_status']) ?>">
                        <?= htmlspecialchars($a['activiteit_status']) ?>
                    </span>
                </div>

                <p class="beschrijving">
                    <?= htmlspecialchars($a['activiteit_beschrijving']) ?>
                </p>

                <div class="card-info">
                    <p><strong>Datum:</strong> <?= htmlspecialchars($a['activiteit_datum']) ?></p>
                    <p><strong>Tijd:</strong> <?= htmlspecialchars($a['activiteit_tijd']) ?></p>
                    <p><strong>Locatie:</strong> <?= htmlspecialchars($a['activiteit_locatie']) ?></p>
                    <p><strong>Soort:</strong> <?= htmlspecialchars($a['soort_activiteit']) ?></p>
                    <?php if (!empty($a['lat']) && !empty($a['lng'])): ?>
                        <p><strong>Coördinaten:</strong> <?= number_format($a['lat'], 4) ?>, <?= number_format($a['lng'], 4) ?></p>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <small><?= htmlspecialchars($a['activiteit_opmerkingen'] ?? '') ?></small>
                </div>

                <div class="card-actions">
                    <a href="#" class="btn-edit" onclick="editActiviteit(<?= $a['activiteit_id'] ?>)">Bewerken</a>
                    <a href="#" class="btn-delete" onclick="deleteActiviteit(<?= $a['activiteit_id'] ?>, '<?= htmlspecialchars($a['activiteit_titel']) ?>')">Verwijderen</a>
                </div>
            </div>
        <?php 
            endforeach;
        endif; 
        ?>
    </div>
</div>

<script>
function editActiviteit(id) {
    console.log('Edit activiteit:', id);
    // TODO: Implementeer edit modal of redirect naar edit pagina
    alert('Edit functionaliteit: activiteit ' + id + '\n\nMaak een edit-pagina aan of voeg een modal toe.');
}

function deleteActiviteit(id, titel) {
    if (confirm('Weet je zeker dat je "' + titel + '" wilt verwijderen?\n\nDeze actie kan niet ongedaan worden.')) {
        // POST request to delete
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('classes/delete_activiteit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Activiteit succesvol verwijderd!');
                location.reload();
            } else {
                alert('Fout bij verwijderen: ' + (data.error || 'Onbekende fout'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Er is een fout opgetreden.');
        });
    }
    return false;
}
</script>

</body>
</html>
