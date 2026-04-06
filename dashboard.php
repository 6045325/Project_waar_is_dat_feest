
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaturebeheer</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/delete-confirm.js"></script>
</head>

<body id="dashboard">
    <!-- DESKTOP & TABLET VERSIE (601px en hoger) -->
    <div class="desktop-admin-container">
        <div class="admin-page-container">
            <div class="admin-action-bar">
                <div class="admin-left-spacer"></div>
                <h1 class="admin-title">Vacaturebeheer</h1>
                <form method="POST" action="addvacature.php">
                    <button type="submit" name="add" class="admin-add-button">+ Nieuwe Vacature</button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Functie</th>
                        <th>Omschrijving</th>
                        <th>Locatie</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vacatures)): ?>
                        <?php foreach ($vacatures as $vacature): ?>
                            <tr>
                                <td><?= htmlspecialchars($vacature['vacature_naam'] ?? $vacature['naam'] ?? 'Onbekend') ?></td>
                                <td><?= htmlspecialchars($vacature['vacature_omschrijving']) ?></td>
                                <td><?= htmlspecialchars($vacature['vacature_locatie'] ?? $vacature['locatie'] ?? '-') ?></td>
                                <td>
                                    <div class="admin-action-buttons">
                                        <a href="addvacature.php?id=<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>" 
                                           class="admin-edit-btn">Bewerken</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vacature-id" 
                                                   value="<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>">
                                            <button type="submit" name="delete" value="1" 
                                                    class="admin-delete-btn">Verwijderen</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-vacatures">Er zijn nog geen vacatures.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBIELE VERSIE (max-width: 600px) -->
    <div class="mobile-admin-container">
        <div class="mobile-admin-header">
            <h1 class="mobile-admin-title">Vacaturebeheer</h1>
            <form method="POST" action="addvacature.php">
                <button type="submit" name="add" class="mobile-add-button">+ Nieuw</button>
            </form>
        </div>

        <div class="mobile-vacature-list">
            <?php if (!empty($vacatures)): ?>
                <?php foreach ($vacatures as $vacature): ?>
                    <div class="mobile-vacature-card">
                        <div class="mobile-vacature-header">
                            <h3 class="mobile-vacature-name">
                                <?= htmlspecialchars($vacature['vacature_naam'] ?? $vacature['naam'] ?? 'Onbekend') ?>
                            </h3>
                            <div class="mobile-vacature-location">
                                📍 <?= htmlspecialchars($vacature['vacature_locatie'] ?? $vacature['locatie'] ?? '-') ?>
                            </div>
                        </div>
                        
                        <p class="mobile-vacature-description">
                            <?= htmlspecialchars($vacature['vacature_omschrijving']) ?>
                        </p>
                        
                        <div class="mobile-vacature-actions">
                            <a href="addvacature.php?id=<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>" 
                               class="mobile-edit-btn">Bewerken</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="vacature-id" 
                                       value="<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>">
                                <button type="submit" name="delete" value="1" 
                                        class="mobile-delete-btn">Verwijderen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mobile-no-vacatures">
                    Er zijn nog geen vacatures.<br>
                    <small>Maak je eerste vacature aan met de "+ Nieuw" knop.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Bevestiging voor verwijderen op alle apparaten
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.admin-delete-btn, .mobile-delete-btn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Weet je zeker dat je deze vacature wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>