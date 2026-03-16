<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login/login.php');
    exit;
}

require_once __DIR__ . '/classes/activiteitmanager.php';
$activiteitManager = new ActiviteitManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['activiteit-id'])) {
    $id = (int)$_POST['activiteit-id'];
    if ($id > 0) {
        $activiteit = $activiteitManager->getActiviteitById($id);
        if ($activiteit && (int)$activiteit['user_id'] === (int)$_SESSION['user_id']) {
            $activiteitManager->deleteActiviteit($id);
        }
    }
    header('Location: dashboard.php');
    exit;
}

$allactiviteiten = $activiteitManager->getAllActiviteiten();
$userId = $_SESSION['user_id'] ?? null;
$activiteiten = [];

foreach ($allactiviteiten as $activiteit) {
    if ($userId !== null && (int)$activiteit['user_id'] === (int)$userId) {
        $activiteiten[] = $activiteit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activiteitenbeheer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body id="dashboard">
    <div class="desktop-admin-container">
        <div class="admin-page-container">
            <div class="admin-top-row">
                <div>
                    <p class="admin-subtitle">Welkom, <?= htmlspecialchars($_SESSION['username'] ?? 'gebruiker') ?></p>
                    <h1 class="admin-title">Activiteitenbeheer</h1>
                </div>
                <div class="admin-top-actions">
                    <a href="addactiviteit.php" class="admin-add-button">+ Nieuwe Activiteit</a>
                    <a href="logout.php" class="admin-logout-button">Uitloggen</a>
                </div>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Datum</th>
                        <th>Tijd</th>
                        <th>Locatie</th>
                        <th>Soort</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($activiteiten)): ?>
                        <?php foreach ($activiteiten as $activiteit): ?>
                            <tr>
                                <td><?= htmlspecialchars($activiteit['activiteit_titel']) ?></td>
                                <td><?= htmlspecialchars($activiteit['activiteit_datum']) ?></td>
                                <td><?= htmlspecialchars(substr($activiteit['activiteit_tijd'], 0, 5)) ?></td>
                                <td><?= htmlspecialchars($activiteit['activiteit_locatie']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($activiteit['soort_activiteit'])) ?></td>
                                <td><?= htmlspecialchars(ucfirst($activiteit['activiteit_status'])) ?></td>
                                <td>
                                    <div class="admin-action-buttons">
                                        <a href="addactiviteit.php?id=<?= (int)$activiteit['activiteit_id'] ?>" class="admin-edit-btn">Bewerken</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="activiteit-id" value="<?= (int)$activiteit['activiteit_id'] ?>">
                                            <button type="submit" name="delete" value="1" class="admin-delete-btn">Verwijderen</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-items">Er zijn nog geen activiteiten.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mobile-admin-container">
        <div class="mobile-admin-header">
            <div>
                <p class="admin-subtitle">Welkom, <?= htmlspecialchars($_SESSION['username'] ?? 'gebruiker') ?></p>
                <h1 class="mobile-admin-title">Activiteitenbeheer</h1>
            </div>
            <a href="addactiviteit.php" class="mobile-add-button">+ Nieuw</a>
        </div>
        <a href="logout.php" class="mobile-logout-link">Uitloggen</a>

        <div class="mobile-activiteit-list">
            <?php if (!empty($activiteiten)): ?>
                <?php foreach ($activiteiten as $activiteit): ?>
                    <div class="mobile-activiteit-card">
                        <div class="mobile-activiteit-header">
                            <h3 class="mobile-activiteit-name"><?= htmlspecialchars($activiteit['activiteit_titel']) ?></h3>
                            <div class="mobile-activiteit-meta">📅 <?= htmlspecialchars($activiteit['activiteit_datum']) ?> · 🕒 <?= htmlspecialchars(substr($activiteit['activiteit_tijd'], 0, 5)) ?></div>
                            <div class="mobile-activiteit-meta">📍 <?= htmlspecialchars($activiteit['activiteit_locatie']) ?></div>
                            <div class="mobile-activiteit-meta">Soort: <?= htmlspecialchars(ucfirst($activiteit['soort_activiteit'])) ?> · Status: <?= htmlspecialchars(ucfirst($activiteit['activiteit_status'])) ?></div>
                        </div>
                        <p class="mobile-activiteit-description"><?= nl2br(htmlspecialchars($activiteit['activiteit_beschrijving'])) ?></p>
                        <?php if (!empty($activiteit['activiteit_opmerkingen'])): ?>
                            <p class="mobile-activiteit-notes"><strong>Opmerkingen:</strong> <?= nl2br(htmlspecialchars($activiteit['activiteit_opmerkingen'])) ?></p>
                        <?php endif; ?>
                        <div class="mobile-activiteit-actions">
                            <a href="addactiviteit.php?id=<?= (int)$activiteit['activiteit_id'] ?>" class="mobile-edit-btn">Bewerken</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="activiteit-id" value="<?= (int)$activiteit['activiteit_id'] ?>">
                                <button type="submit" name="delete" value="1" class="mobile-delete-btn">Verwijderen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mobile-no-items">Er zijn nog geen activiteiten. Maak je eerste activiteit aan met de knop hierboven.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.admin-delete-btn, .mobile-delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Weet je zeker dat je deze activiteit wilt verwijderen?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
