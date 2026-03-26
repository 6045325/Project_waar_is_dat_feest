
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
    <div class="page-header">
        <h1>Activiteiten</h1>
        <button id="add-activity-btn" class="btn-add">Nieuwe Activiteit Toevoegen</button>
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

                        <div class="card-actions">
                            <a href="#" class="btn-edit" onclick="editActiviteit(<?= $a['activiteit_id'] ?>)">Bewerken</a>
                            <a href="#" class="btn-delete" onclick="deleteActiviteit(<?= $a['activiteit_id'] ?>, '<?= htmlspecialchars($a['activiteit_titel']) ?>')">Verwijderen</a>
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
            <div class="form-group">
                <label for="titel">Titel *</label>
                <input type="text" id="titel" name="titel" required>
            </div>

            <div class="form-group">
                <label for="beschrijving">Beschrijving *</label>
                <textarea id="beschrijving" name="beschrijving" rows="4" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="datum">Datum *</label>
                    <input type="date" id="datum" name="datum" required>
                </div>
                <div class="form-group">
                    <label for="tijd">Tijd *</label>
                    <input type="time" id="tijd" name="tijd" required>
                </div>
            </div>

            <div class="form-group">
                <label for="locatie">Locatie *</label>
                <input type="text" id="locatie" name="locatie" required placeholder="bijv. Amsterdam, Rotterdam">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="soort">Soort Activiteit</label>
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
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="gepland">Gepland</option>
                        <option value="actief">Actief</option>
                        <option value="inactief">Inactief</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="image_url">Afbeelding URL</label>
                <input type="url" id="image_url" name="image_url" placeholder="https://example.com/afbeelding.jpg">
            </div>

            <div class="form-group">
                <label for="opmerkingen">Opmerkingen</label>
                <textarea id="opmerkingen" name="opmerkingen" rows="2" placeholder="Optionele opmerkingen"></textarea>
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
            <div class="form-group">
                <label for="edit-titel">Titel *</label>
                <input type="text" id="edit-titel" name="titel" required>
            </div>

            <div class="form-group">
                <label for="edit-beschrijving">Beschrijving *</label>
                <textarea id="edit-beschrijving" name="beschrijving" rows="4" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit-datum">Datum *</label>
                    <input type="date" id="edit-datum" name="datum" required>
                </div>
                <div class="form-group">
                    <label for="edit-tijd">Tijd *</label>
                    <input type="time" id="edit-tijd" name="tijd" required>
                </div>
            </div>

            <div class="form-group">
                <label for="edit-locatie">Locatie *</label>
                <input type="text" id="edit-locatie" name="locatie" required placeholder="bijv. Amsterdam, Rotterdam">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit-soort">Soort Activiteit</label>
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
                    <label for="edit-status">Status</label>
                    <select id="edit-status" name="status">
                        <option value="gepland">Gepland</option>
                        <option value="actief">Actief</option>
                        <option value="inactief">Inactief</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="edit-image_url">Afbeelding URL</label>
                <input type="url" id="edit-image_url" name="image_url" placeholder="https://example.com/afbeelding.jpg">
            </div>

            <div class="form-group">
                <label for="edit-opmerkingen">Opmerkingen</label>
                <textarea id="edit-opmerkingen" name="opmerkingen" rows="2" placeholder="Optionele opmerkingen"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-edit-cancel">Annuleren</button>
                <button type="submit" class="btn-submit">Activiteit Bijwerken</button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functionaliteit
const modal = document.getElementById('add-activity-modal');
const addBtn = document.getElementById('add-activity-btn');
const closeBtn = document.querySelector('.close');
const cancelBtn = document.querySelector('.btn-cancel');
const form = document.getElementById('add-activity-form');

// Edit modal
const editModal = document.getElementById('edit-activity-modal');
const editCloseBtn = document.querySelector('.edit-close');
const editCancelBtn = document.querySelector('.btn-edit-cancel');
const editForm = document.getElementById('edit-activity-form');

// Modal openen
addBtn.onclick = function() {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Modal sluiten functies
function closeModal() {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    form.reset();
}

function closeEditModal() {
    editModal.style.display = 'none';
    document.body.style.overflow = 'auto';
    editForm.reset();
}

closeBtn.onclick = closeModal;
cancelBtn.onclick = closeModal;
editCloseBtn.onclick = closeEditModal;
editCancelBtn.onclick = closeEditModal;

// Klik buiten modal om te sluiten
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Formulier verwerken voor toevoegen
form.onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(form);
    
    // Voeg user_id toe (voor nu hardcoded, later uit session)
    formData.append('user_id', 1); // TODO: Haal uit session
    
    fetch('classes/add_activiteit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activiteit succesvol toegevoegd!');
            closeModal();
            location.reload(); // Herlaad pagina om nieuwe activiteit te tonen
        } else {
            alert('Fout bij toevoegen: ' + (data.error || 'Onbekende fout'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden.');
    });
}

// Edit formulier verwerken
editForm.onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(editForm);
    
    fetch('classes/update_activiteit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activiteit succesvol bijgewerkt!');
            closeEditModal();
            location.reload();
        } else {
            alert('Fout bij bijwerken: ' + (data.error || 'Onbekende fout'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden.');
    });
}

// Bestaande functies
function editActiviteit(id) {
    // Haal activiteit data op
    fetch('classes/get_activiteit.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const activiteit = data.data;
            
            // Vul de form in
            document.getElementById('edit-id').value = activiteit.activiteit_id;
            document.getElementById('edit-titel').value = activiteit.activiteit_titel;
            document.getElementById('edit-beschrijving').value = activiteit.activiteit_beschrijving;
            document.getElementById('edit-datum').value = activiteit.activiteit_datum;
            document.getElementById('edit-tijd').value = activiteit.activiteit_tijd;
            document.getElementById('edit-locatie').value = activiteit.activiteit_locatie;
            document.getElementById('edit-soort').value = activiteit.soort_activiteit;
            document.getElementById('edit-status').value = activiteit.activiteit_status;
            document.getElementById('edit-image_url').value = activiteit.activiteit_afbeelding_url || '';
            document.getElementById('edit-opmerkingen').value = activiteit.activiteit_opmerkingen || '';
            
            // Open modal
            editModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } else {
            alert('Fout bij ophalen activiteit: ' + (data.error || 'Onbekende fout'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden bij het ophalen van de activiteit.');
    });
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
