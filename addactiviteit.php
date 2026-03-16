<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login/login.php');
    exit;
}

require_once __DIR__ . '/classes/activiteitmanager.php';
$activiteitManager = new ActiviteitManager();

const TAB_DATA = [
    'basis' => [
        'title' => 'Basis',
        'subtitle' => 'Titel en beschrijving',
        'fields' => ['activiteit_titel', 'activiteit_beschrijving']
    ],
    'planning' => [
        'title' => 'Planning',
        'subtitle' => 'Datum, tijd en status',
        'fields' => ['activiteit_datum', 'activiteit_tijd', 'activiteit_status']
    ],
    'locatie' => [
        'title' => 'Locatie',
        'subtitle' => 'Waar en wat voor soort',
        'fields' => ['activiteit_locatie', 'soort_activiteit']
    ],
    'extra' => [
        'title' => 'Extra',
        'subtitle' => 'Opmerkingen',
        'fields' => ['activiteit_opmerkingen']
    ]
];

$isEditMode = isset($_GET['id']) && is_numeric($_GET['id']);
$activiteitId = $isEditMode ? (int)$_GET['id'] : 0;
$errors = [];
$activeTab = $_POST['active_tab'] ?? array_key_first(TAB_DATA);
$activiteit = [
    'activiteit_titel' => '',
    'activiteit_beschrijving' => '',
    'activiteit_datum' => '',
    'activiteit_tijd' => '',
    'activiteit_locatie' => '',
    'soort_activiteit' => 'binnen',
    'activiteit_status' => 'gepland',
    'activiteit_opmerkingen' => ''
];

if ($isEditMode) {
    $gevonden = $activiteitManager->getActiviteitById($activiteitId);
    if (!$gevonden || (int)$gevonden['user_id'] !== (int)($_SESSION['user_id'] ?? 0)) {
        die('Activiteit niet gevonden of geen toegang.');
    }
    $activiteit = array_merge($activiteit, $gevonden);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($activiteit) as $key) {
        if (isset($_POST[$key])) {
            $activiteit[$key] = trim((string)$_POST[$key]);
        }
    }

    if ($activiteit['activiteit_titel'] === '') $errors['activiteit_titel'] = 'Titel is verplicht.';
    if ($activiteit['activiteit_beschrijving'] === '') $errors['activiteit_beschrijving'] = 'Beschrijving is verplicht.';
    if ($activiteit['activiteit_datum'] === '') $errors['activiteit_datum'] = 'Datum is verplicht.';
    if ($activiteit['activiteit_tijd'] === '') $errors['activiteit_tijd'] = 'Tijd is verplicht.';
    if ($activiteit['activiteit_locatie'] === '') $errors['activiteit_locatie'] = 'Locatie is verplicht.';
    if (!in_array($activiteit['soort_activiteit'], ['binnen', 'buiten'], true)) $errors['soort_activiteit'] = 'Kies binnen of buiten.';
    if (!in_array($activiteit['activiteit_status'], ['gepland', 'geannuleerd', 'voltooid'], true)) $errors['activiteit_status'] = 'Kies een geldige status.';

    if (empty($errors)) {
        if ($isEditMode) {
            $gelukt = $activiteitManager->updateActiviteit(
                $activiteitId,
                $activiteit['activiteit_titel'],
                $activiteit['activiteit_beschrijving'],
                $activiteit['activiteit_datum'],
                $activiteit['activiteit_tijd'],
                $activiteit['activiteit_locatie'],
                $activiteit['soort_activiteit'],
                $activiteit['activiteit_status'],
                $activiteit['activiteit_opmerkingen']
            );
        } else {
            $gelukt = $activiteitManager->addActiviteit(
                $activiteit['activiteit_titel'],
                $activiteit['activiteit_beschrijving'],
                $activiteit['activiteit_datum'],
                $activiteit['activiteit_tijd'],
                $activiteit['activiteit_locatie'],
                $activiteit['soort_activiteit'],
                $activiteit['activiteit_status'],
                $activiteit['activiteit_opmerkingen'],
                (int)$_SESSION['user_id']
            );
        }

        if ($gelukt) {
            header('Location: dashboard.php');
            exit;
        }
        $errors['db'] = 'Er ging iets mis bij het opslaan.';
    } else {
        foreach (TAB_DATA as $tabId => $tab) {
            foreach ($tab['fields'] as $field) {
                if (isset($errors[$field])) {
                    $activeTab = $tabId;
                    break 2;
                }
            }
        }
    }
}

function fieldValue(array $activiteit, string $key): string {
    return htmlspecialchars((string)($activiteit[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

function fieldFilled(array $activiteit, string $field): bool {
    return trim((string)($activiteit[$field] ?? '')) !== '';
}

function progressForTab(array $activiteit, string $tabId, array $fields): int {
    $progressFieldsMap = [
        'basis' => ['activiteit_titel', 'activiteit_beschrijving'],
        'planning' => ['activiteit_datum', 'activiteit_tijd'],
        'locatie' => ['activiteit_locatie'],
        'extra' => ['activiteit_opmerkingen']
    ];

    $progressFields = $progressFieldsMap[$tabId] ?? $fields;
    if (count($progressFields) === 0) {
        return 0;
    }

    $filled = 0;
    foreach ($progressFields as $field) {
        if (fieldFilled($activiteit, $field)) {
            $filled++;
        }
    }

    return (int)round(($filled / count($progressFields)) * 100);
}

$progressFields = ['activiteit_titel', 'activiteit_beschrijving', 'activiteit_datum', 'activiteit_tijd', 'activiteit_locatie'];
$totalRequired = count($progressFields);
$totalFilled = 0;
foreach ($progressFields as $field) {
    if (fieldFilled($activiteit, $field)) {
        $totalFilled++;
    }
}
$globalProgress = $totalRequired > 0 ? (int)round(($totalFilled / $totalRequired) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditMode ? 'Activiteit bewerken' : 'Nieuwe activiteit toevoegen' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body id="activity-page">
    <div class="wizard-page">
        <div class="wizard-shell">
            <div class="wizard-topbar">
                <div>
                    <p class="wizard-kicker"><?= $isEditMode ? 'Bewerk je activiteit' : 'Nieuwe activiteit aanmaken' ?></p>
                    <h1 class="wizard-title"><?= $isEditMode ? 'Activiteit bewerken' : 'Nieuwe activiteit' ?></h1>
                    <p class="wizard-intro">Vul stap voor stap alle informatie in. Een onderdeel kleurt groen zodra de verplichte velden van die stap zijn ingevuld.</p>
                </div>
                <a href="dashboard.php" class="wizard-back-link">← Terug naar overzicht</a>
            </div>

            <?php if (isset($errors['db'])): ?>
                <div class="wizard-alert"><?= htmlspecialchars($errors['db']) ?></div>
            <?php endif; ?>

            <div class="wizard-card">
                <div class="wizard-tabs-shell">
                    <nav class="wizard-tabs" role="tablist">
                        <?php foreach (TAB_DATA as $tabId => $tab):
                            $tabProgress = progressForTab($activiteit, $tabId, $tab['fields']);
                            $isActive = $activeTab === $tabId;
                        ?>
                            <button
                                type="button"
                                class="wizard-tab<?= $isActive ? ' active' : '' ?><?= $tabProgress === 100 ? ' complete' : '' ?>"
                                data-tab="<?= $tabId ?>"
                                role="tab"
                                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                            >
                                <span class="wizard-tab-step">Stap</span>
                                <span class="wizard-tab-title"><?= htmlspecialchars($tab['title']) ?></span>
                                <span class="wizard-tab-subtitle"><?= htmlspecialchars($tab['subtitle']) ?></span>
                                <span class="wizard-progress-track small">
                                    <span class="wizard-progress-fill" data-progress-for="<?= $tabId ?>" style="width: <?= $tabProgress ?>%"></span>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <div class="wizard-inline-progress">
                    <span>Voortgang formulier</span>
                    <strong id="globalProgressLabel"><?= $globalProgress ?>%</strong>
                </div>

                <form method="POST" id="activityWizardForm" class="wizard-form">
                    <input type="hidden" name="active_tab" id="active_tab" value="<?= htmlspecialchars($activeTab) ?>">

                    <section class="wizard-panel<?= $activeTab === 'basis' ? ' active' : '' ?>" data-panel="basis" <?= $activeTab === 'basis' ? '' : 'hidden' ?>>
                        <div class="wizard-panel-header">
                            <h2>Basisinformatie</h2>
                            <p>Geef je activiteit een duidelijke naam en korte uitleg.</p>
                        </div>

                        <div class="wizard-field full">
                            <label for="activiteit_titel">Titel</label>
                            <input type="text" id="activiteit_titel" name="activiteit_titel" value="<?= fieldValue($activiteit, 'activiteit_titel') ?>" required>
                            <div class="wizard-error<?= isset($errors['activiteit_titel']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_titel'] ?? 'Titel is verplicht.') ?></div>
                        </div>

                        <div class="wizard-field full">
                            <label for="activiteit_beschrijving">Beschrijving</label>
                            <textarea id="activiteit_beschrijving" name="activiteit_beschrijving" rows="6" required><?= fieldValue($activiteit, 'activiteit_beschrijving') ?></textarea>
                            <div class="wizard-error<?= isset($errors['activiteit_beschrijving']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_beschrijving'] ?? 'Beschrijving is verplicht.') ?></div>
                        </div>
                    </section>

                    <section class="wizard-panel<?= $activeTab === 'planning' ? ' active' : '' ?>" data-panel="planning" <?= $activeTab === 'planning' ? '' : 'hidden' ?>>
                        <div class="wizard-panel-header">
                            <h2>Planning</h2>
                            <p>Kies de datum, tijd en status van de activiteit.</p>
                        </div>

                        <div class="wizard-grid two">
                            <div class="wizard-field">
                                <label for="activiteit_datum">Datum</label>
                                <input type="date" id="activiteit_datum" name="activiteit_datum" value="<?= fieldValue($activiteit, 'activiteit_datum') ?>" required>
                                <div class="wizard-error<?= isset($errors['activiteit_datum']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_datum'] ?? 'Datum is verplicht.') ?></div>
                            </div>

                            <div class="wizard-field">
                                <label for="activiteit_tijd">Tijd</label>
                                <input type="time" id="activiteit_tijd" name="activiteit_tijd" value="<?= fieldValue($activiteit, 'activiteit_tijd') ?>" required>
                                <div class="wizard-error<?= isset($errors['activiteit_tijd']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_tijd'] ?? 'Tijd is verplicht.') ?></div>
                            </div>
                        </div>

                        <div class="wizard-field full">
                            <label for="activiteit_status">Status</label>
                            <select id="activiteit_status" name="activiteit_status">
                                <option value="gepland" <?= ($activiteit['activiteit_status'] === 'gepland') ? 'selected' : '' ?>>Gepland</option>
                                <option value="geannuleerd" <?= ($activiteit['activiteit_status'] === 'geannuleerd') ? 'selected' : '' ?>>Geannuleerd</option>
                                <option value="voltooid" <?= ($activiteit['activiteit_status'] === 'voltooid') ? 'selected' : '' ?>>Voltooid</option>
                            </select>
                            <div class="wizard-helper">Status heeft een standaardwaarde en telt niet mee voor de groene voortgang.</div>
                            <div class="wizard-error<?= isset($errors['activiteit_status']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_status'] ?? 'Kies een geldige status.') ?></div>
                        </div>
                    </section>

                    <section class="wizard-panel<?= $activeTab === 'locatie' ? ' active' : '' ?>" data-panel="locatie" <?= $activeTab === 'locatie' ? '' : 'hidden' ?>>
                        <div class="wizard-panel-header">
                            <h2>Locatie</h2>
                            <p>Waar is de activiteit en is het binnen of buiten?</p>
                        </div>

                        <div class="wizard-field full">
                            <label for="activiteit_locatie">Locatie</label>
                            <input type="text" id="activiteit_locatie" name="activiteit_locatie" value="<?= fieldValue($activiteit, 'activiteit_locatie') ?>" required>
                            <div class="wizard-error<?= isset($errors['activiteit_locatie']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['activiteit_locatie'] ?? 'Locatie is verplicht.') ?></div>
                        </div>

                        <div class="wizard-field full">
                            <label for="soort_activiteit">Soort activiteit</label>
                            <select id="soort_activiteit" name="soort_activiteit">
                                <option value="binnen" <?= ($activiteit['soort_activiteit'] === 'binnen') ? 'selected' : '' ?>>Binnen</option>
                                <option value="buiten" <?= ($activiteit['soort_activiteit'] === 'buiten') ? 'selected' : '' ?>>Buiten</option>
                            </select>
                            <div class="wizard-helper">Ook dit veld heeft een standaardkeuze en start dus niet automatisch groen.</div>
                            <div class="wizard-error<?= isset($errors['soort_activiteit']) ? ' visible' : '' ?>"><?= htmlspecialchars($errors['soort_activiteit'] ?? 'Kies een geldige soort activiteit.') ?></div>
                        </div>
                    </section>

                    <section class="wizard-panel<?= $activeTab === 'extra' ? ' active' : '' ?>" data-panel="extra" <?= $activeTab === 'extra' ? '' : 'hidden' ?>>
                        <div class="wizard-panel-header">
                            <h2>Extra informatie</h2>
                            <p>Voeg eventueel opmerkingen toe voor jezelf of andere gebruikers.</p>
                        </div>

                        <div class="wizard-field full">
                            <label for="activiteit_opmerkingen">Opmerkingen</label>
                            <textarea id="activiteit_opmerkingen" name="activiteit_opmerkingen" rows="6"><?= fieldValue($activiteit, 'activiteit_opmerkingen') ?></textarea>
                            <div class="wizard-helper">Optioneel veld</div>
                        </div>
                    </section>

                    <div class="wizard-footer">
                        <button type="button" class="wizard-btn wizard-btn-secondary" id="prevStep">← Vorige</button>
                        <div class="wizard-footer-right">
                            <a href="dashboard.php" class="wizard-btn wizard-btn-ghost">Annuleren</a>
                            <button type="button" class="wizard-btn wizard-btn-primary" id="nextStep">Volgende →</button>
                            <button type="submit" class="wizard-btn wizard-btn-primary" id="saveStep"><?= $isEditMode ? 'Wijzigingen opslaan' : 'Activiteit opslaan' ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tabs = Array.from(document.querySelectorAll('.wizard-tab'));
        const panels = Array.from(document.querySelectorAll('.wizard-panel'));
        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');
        const saveBtn = document.getElementById('saveStep');
        const activeTabInput = document.getElementById('active_tab');
        const currentErrorIds = <?= json_encode(array_keys($errors)) ?>;
        const requiredPerTab = {
            basis: ['activiteit_titel', 'activiteit_beschrijving'],
            planning: ['activiteit_datum', 'activiteit_tijd', 'activiteit_status'],
            locatie: ['activiteit_locatie', 'soort_activiteit'],
            extra: []
        };
        const progressPerTab = {
            basis: ['activiteit_titel', 'activiteit_beschrijving'],
            planning: ['activiteit_datum', 'activiteit_tijd'],
            locatie: ['activiteit_locatie'],
            extra: ['activiteit_opmerkingen']
        };
        const progressAllFields = ['activiteit_titel', 'activiteit_beschrijving', 'activiteit_datum', 'activiteit_tijd', 'activiteit_locatie'];
        const tabOrder = Object.keys(requiredPerTab);
        let currentTab = activeTabInput.value || tabOrder[0];

        function fieldValid(id) {
            const field = document.getElementById(id);
            if (!field) return true;
            return field.value.trim() !== '';
        }

        function tabProgress(tabId) {
            const fields = progressPerTab[tabId] || [];
            if (!fields.length) return 0;
            const filled = fields.filter(fieldValid).length;
            return Math.round((filled / fields.length) * 100);
        }

        function updateProgress() {
            tabs.forEach(tab => {
                const tabId = tab.dataset.tab;
                const progress = tabProgress(tabId);
                const fill = document.querySelector(`[data-progress-for="${tabId}"]`);
                if (fill) fill.style.width = progress + '%';
                tab.classList.toggle('complete', progress === 100);
            });

            const filledRequired = progressAllFields.filter(fieldValid).length;
            const totalRequired = progressAllFields.length;
            const globalProgress = totalRequired ? Math.round((filledRequired / totalRequired) * 100) : 0;
            document.getElementById('globalProgressLabel').textContent = globalProgress + '%';
        }

        function showTab(tabId) {
            currentTab = tabId;
            activeTabInput.value = tabId;

            tabs.forEach(tab => {
                const isActive = tab.dataset.tab === tabId;
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            panels.forEach(panel => {
                const isActive = panel.dataset.panel === tabId;
                panel.classList.toggle('active', isActive);
                panel.hidden = !isActive;
            });

            const currentIndex = tabOrder.indexOf(tabId);
            prevBtn.style.visibility = currentIndex === 0 ? 'hidden' : 'visible';
            nextBtn.style.display = currentIndex === tabOrder.length - 1 ? 'none' : 'inline-flex';
            saveBtn.style.display = currentIndex === tabOrder.length - 1 ? 'inline-flex' : 'none';
        }

        function validateCurrentTab() {
            const fields = requiredPerTab[currentTab] || [];
            let valid = true;
            fields.forEach(id => {
                const field = document.getElementById(id);
                const error = field.closest('.wizard-field').querySelector('.wizard-error');
                if (field.value.trim() === '') {
                    valid = false;
                    field.classList.add('error');
                    if (error) error.classList.add('visible');
                } else {
                    field.classList.remove('error');
                    if (error && !currentErrorIds.includes(id)) error.classList.remove('visible');
                }
            });
            return valid;
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                const currentIndex = tabOrder.indexOf(currentTab);
                const targetIndex = tabOrder.indexOf(target);

                if (targetIndex > currentIndex && !validateCurrentTab()) return;
                showTab(target);
            });
        });

        nextBtn.addEventListener('click', () => {
            if (!validateCurrentTab()) return;
            const nextIndex = tabOrder.indexOf(currentTab) + 1;
            if (nextIndex < tabOrder.length) showTab(tabOrder[nextIndex]);
        });

        prevBtn.addEventListener('click', () => {
            const prevIndex = tabOrder.indexOf(currentTab) - 1;
            if (prevIndex >= 0) showTab(tabOrder[prevIndex]);
        });

        document.querySelectorAll('.wizard-field input, .wizard-field textarea, .wizard-field select').forEach(field => {
            field.addEventListener('input', () => {
                field.classList.remove('error');
                const error = field.closest('.wizard-field').querySelector('.wizard-error');
                if (error && !currentErrorIds.includes(field.id)) error.classList.remove('visible');
                updateProgress();
            });
            field.addEventListener('change', () => {
                field.classList.remove('error');
                const error = field.closest('.wizard-field').querySelector('.wizard-error');
                if (error && !currentErrorIds.includes(field.id)) error.classList.remove('visible');
                updateProgress();
            });
        });

        showTab(currentTab);
        updateProgress();
    </script>
</body>
</html>
