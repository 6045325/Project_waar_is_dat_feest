
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

    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Pacifico&family=Fredoka&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Leaflet Map Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/weather.js"></script>
    <script src="js/map.js"></script>
    <script src="js/modal-manager.js"></script>
    <script src="js/icon-init.js"></script>
</head>
<body>

<div id="Activiteiten-page">
    <?php require_once 'includes/navbar.php'; ?>
</div>

<div class="container">
    <div class="page-header">
        <h1>Activiteiten</h1>
        <button id="add-activity-btn" class="btn-add">Nieuwe Activiteit Toevoegen</button>
    </div>

    <!-- Kaart Container -->
    <div class="map-container">
        <h2>📍 Activiteiten Kaart</h2>
        <div id="activity-map"></div>
    </div>

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
                <select name="distance" id="distanceSelect" class="distance-select">
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
            <div class="card" onclick="openDetailModal(<?= $a['activiteit_id'] ?>)" style="cursor: pointer;" data-activity-id="<?= $a['activiteit_id'] ?>" data-lat="<?= $a['lat'] ?? '' ?>" data-lng="<?= $a['lng'] ?? '' ?>">
                <div class="card-left">
                    <div class="card-header">
                        <h2><?= htmlspecialchars($a['activiteit_titel']) ?></h2>
                        <span class="status <?= htmlspecialchars($a['activiteit_status']) ?>">
                            <?= htmlspecialchars($a['activiteit_status']) ?>
                        </span>
                    </div>

                    <div class="card-content">
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

                        <!-- Weather Widget -->
                        <div class="weather-widget"></div>

                        <div class="card-actions">
                            <a href="#" class="btn-edit" onclick="event.stopPropagation(); editActiviteit(<?= $a['activiteit_id'] ?>); return false;">Bewerken</a>
                            <a href="#" class="btn-delete" onclick="event.stopPropagation(); deleteActiviteit(<?= $a['activiteit_id'] ?>, '<?= htmlspecialchars($a['activiteit_titel']) ?>'); return false;">Verwijderen</a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($a['activiteit_afbeelding_url'])): ?>
                    <div class="card-right">
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($a['activiteit_afbeelding_url']) ?>" alt="<?= htmlspecialchars($a['activiteit_titel']) ?>">
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card-right">
                        <div class="no-image">
                            <p>Geen afbeelding beschikbaar</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php 
            endforeach;
        endif; 
        ?>
    </div>
</div>

<!-- Modal voor nieuwe activiteit -->
<div id="add-activity-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Nieuwe Activiteit Toevoegen</h2>
            <span class="close">&times;</span>
        </div>
        <form id="add-activity-form" class="activity-form">
            <div class="form-section">
                <div class="form-group">
                    <label for="titel"><i data-lucide="pencil"></i> Titel *</label>
                    <input type="text" id="titel" name="titel" required>
                </div>

                <div class="form-group">
                    <label for="beschrijving"><i data-lucide="file-text"></i> Beschrijving *</label>
                    <textarea id="beschrijving" name="beschrijving" rows="4" required></textarea>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="datum"><i data-lucide="calendar-days"></i> Datum *</label>
                    <input type="date" id="datum" name="datum" required>
                </div>
                <div class="form-group">
                    <label for="tijd"><i data-lucide="clock-10"></i> Tijd *</label>
                    <input type="time" id="tijd" name="tijd" required>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label for="locatie"><i data-lucide="map-pin"></i> Locatie *</label>
                    <input type="text" id="locatie" name="locatie" required placeholder="bijv. Amsterdam, Rotterdam">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="soort"><i data-lucide="flame-kindling"></i> Soort Activiteit</label>
                    <select id="soort" name="soort">
                        <option value="Festival">Festival</option>
                        <option value="Concert">Concert</option>
                        <option value="Sport">Sport</option>
                        <option value="Theater">Theater</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Anders">Anders</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status"><i data-lucide="chart-column"></i> Status</label>
                    <select id="status" name="status">
                        <option value="gepland">Gepland</option>
                        <option value="actief">Actief</option>
                        <option value="inactief">Inactief</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label for="image_url"><i data-lucide="image"></i> Afbeelding URL</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://example.com/afbeelding.jpg">
                </div>

                <div class="form-group">
                    <label for="opmerkingen"><i data-lucide="message-square"></i> Opmerkingen</label>
                    <textarea id="opmerkingen" name="opmerkingen" rows="2" placeholder="Optionele opmerkingen"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel">Annuleren</button>
                <button type="submit" class="btn-submit">Activiteit Toevoegen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal voor activiteit bewerken -->
<div id="edit-activity-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Activiteit Bewerken</h2>
            <span class="edit-close">&times;</span>
        </div>
        <form id="edit-activity-form" class="activity-form">
            <input type="hidden" id="edit-id" name="id">
            <div class="form-section">
                <div class="form-group">
                    <label for="edit-titel"><i data-lucide="pencil"></i> Titel *</label>
                    <input type="text" id="edit-titel" name="titel" required>
                </div>

                <div class="form-group">
                    <label for="edit-beschrijving"><i data-lucide="file-text"></i> Beschrijving *</label>
                    <textarea id="edit-beschrijving" name="beschrijving" rows="4" required></textarea>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit-datum"><i data-lucide="calendar-days"></i> Datum *</label>
                    <input type="date" id="edit-datum" name="datum" required>
                </div>
                <div class="form-group">
                    <label for="edit-tijd"><i data-lucide="clock-10"></i> Tijd *</label>
                    <input type="time" id="edit-tijd" name="tijd" required>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label for="edit-locatie"><i data-lucide="map-pin"></i> Locatie *</label>
                    <input type="text" id="edit-locatie" name="locatie" required placeholder="bijv. Amsterdam, Rotterdam">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit-soort"><i data-lucide="flame-kindling"></i> Soort Activiteit</label>
                    <select id="edit-soort" name="soort">
                        <option value="Festival">Festival</option>
                        <option value="Concert">Concert</option>
                        <option value="Sport">Sport</option>
                        <option value="Theater">Theater</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Anders">Anders</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-status"><i data-lucide="chart-column"></i> Status</label>
                    <select id="edit-status" name="status">
                        <option value="gepland">Gepland</option>
                        <option value="actief">Actief</option>
                        <option value="inactief">Inactief</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div class="form-group">
                    <label for="edit-image_url"><i data-lucide="image"></i> Afbeelding URL</label>
                    <input type="url" id="edit-image_url" name="image_url" placeholder="https://example.com/afbeelding.jpg">
                </div>

                <div class="form-group">
                    <label for="edit-opmerkingen"><i data-lucide="message-square"></i> Opmerkingen</label>
                    <textarea id="edit-opmerkingen" name="opmerkingen" rows="2" placeholder="Optionele opmerkingen"></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-edit-cancel">Annuleren</button>
                <button type="submit" class="btn-submit">Activiteit Bijwerken</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal voor activiteit detail bekijken -->
<div id="detail-activity-modal" class="modal">
    <div class="modal-content detail-modal-content">
        <div class="modal-header">
            <h2 id="detail-titel">Activiteit Details</h2>
            <span class="detail-close">&times;</span>
        </div>
        <div id="detail-content" class="activity-detail">
            <!-- Content wordt geladen via JavaScript -->
        </div>
    </div>
</div>

<!-- Load JS modules -->

<!-- Load JS modules -->
<script type="module" src="js/main.js"></script>
</body>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
// Initialize icons when DOM is ready
function initializeIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', initializeIcons);

// Also call after detail modal content is loaded
const originalFetch = window.fetch;
window.fetch = function(...args) {
    return originalFetch.apply(this, args).then(response => {
        response.clone().json().then(() => {
            initializeIcons();
        }).catch(() => {});
        return response;
    });
};
</script>
</body>
</html>
