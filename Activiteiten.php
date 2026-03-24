
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'classes/activiteitmanager.php';

$manager = new ActiviteitenManager();
$activiteiten = $manager->getAllActiviteiten(); 
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

    <div id="activiteiten-lijst" class="cards">
        <?php foreach ($activiteiten as $a): ?>
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
                    <p><strong>Datum:</strong> <?= $a['activiteit_datum'] ?></p>
                    <p><strong>Tijd:</strong> <?= $a['activiteit_tijd'] ?></p>
                    <p><strong>Locatie:</strong> <?= htmlspecialchars($a['activiteit_locatie']) ?></p>
                    <p><strong>Soort:</strong> <?= htmlspecialchars($a['soort_activiteit']) ?></p>
                </div>

                <div class="card-footer">
                    <small><?= htmlspecialchars($a['activiteit_opmerkingen'] ?? '') ?></small>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
