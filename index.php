<?php
$title = "welcome";

require_once 'classes/activiteitmanager.php';
$manager = new ActiviteitenManager();
$activiteiten = $manager->getAllActiviteiten();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Voeg dit in je <head> -->
<link href="https://fonts.googleapis.com/css2?family=Lobster&family=Pacifico&family=Fredoka&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body id="Index-page">

    <?php require_once 'includes/navbar.php'; ?>

    <!-- Hero -->
    <div class="hero">
        <h1><span class="ontdek">Ontdek</span>, <span class="beleef">beleef</span> en <span class="geniet">geniet</span><br>van de leukste activiteiten</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <p>
            Welkom op dé plek waar avontuur begint. Of je nu op zoek bent naar een leuk dagje uit,
            een sportieve uitdaging of een gezellige activiteit met vrienden, familie of collega’s —
            hier vind je altijd iets dat bij je past.
        </p>

        <h2>Voor ieder moment iets te doen</h2>
        <p>
            Van outdoor avonturen en creatieve workshops tot spannende groepsactiviteiten
            en ontspannende uitjes. Ontdek nieuwe ervaringen en vind altijd iets leuks
            bij jou in de buurt.
        </p>

        <h2>Makkelijk te vinden, snel te boeken</h2>
        <p>
            Met slechts een paar klikken vind je de perfecte activiteit. Filter op locatie,
            type activiteit of budget en plan direct jouw volgende uitje.
        </p>

        <h2>Samen herinneringen maken</h2>
        <p>
            De beste momenten beleef je samen. Of het nu gaat om een verjaardag,
            teamuitje, familiedag of gewoon een spontaan dagje weg —
            wij helpen je om er iets bijzonders van te maken.
        </p>

        <p>
            <strong>Ontdek vandaag nog de leukste activiteiten en plan jouw volgende avontuur!</strong>
        </p>
    </div>

    <!-- Cards -->
    <div class="card-section">
        <div class="card-grid" id="activitySlider">
            <?php if (empty($activiteiten)): ?>
                <div class="no-results">
                    <p>Geen activiteiten gevonden</p>
                </div>
            <?php else: ?>
                <?php foreach ($activiteiten as $a): ?>
                    <div class="card" data-slide>
                        <div class="card-left">
                            <div class="card-header">
                                <h2><?= htmlspecialchars($a['activiteit_titel'] ?? $a['title'] ?? 'Onbekende activiteit') ?></h2>
                                <span class="status <?= htmlspecialchars($a['activiteit_status'] ?? 'onbekend') ?>">
                                    <?= htmlspecialchars($a['activiteit_status'] ?? 'onbekend') ?>
                                </span>
                            </div>

                            <div class="card-content">
                                <p class="beschrijving">
                                    <?= htmlspecialchars($a['activiteit_beschrijving'] ?? $a['text'] ?? 'Geen beschrijving beschikbaar') ?>
                                </p>

                                <div class="card-info">
                                    <p><strong>Datum:</strong> <?= htmlspecialchars($a['activiteit_datum'] ?? '-') ?></p>
                                    <p><strong>Tijd:</strong> <?= htmlspecialchars($a['activiteit_tijd'] ?? '-') ?></p>
                                    <p><strong>Locatie:</strong> <?= htmlspecialchars($a['activiteit_locatie'] ?? '-') ?></p>
                                    <p><strong>Soort:</strong> <?= htmlspecialchars($a['soort_activiteit'] ?? '-') ?></p>
                                </div>

                                <div class="card-footer">
                                    <small><?= htmlspecialchars($a['activiteit_opmerkingen'] ?? '') ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="card-right">
                            <?php if (!empty($a['activiteit_afbeelding_url'])): ?>
                                <div class="card-image">
                                    <img src="<?= htmlspecialchars($a['activiteit_afbeelding_url']) ?>" alt="<?= htmlspecialchars($a['activiteit_titel'] ?? 'Activiteit') ?>">
                                </div>
                            <?php else: ?>
                                <div class="no-image">
                                    <p>Geen afbeelding beschikbaar</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (function() {
            const slider = document.getElementById('activitySlider');
            const slides = slider?.querySelectorAll('[data-slide]');
            let currentIndex = 0;

            function getCardWidth() {
                if (!slides || slides.length === 0) return 0;
                const card = slides[0];
                const style = window.getComputedStyle(card);
                return card.offsetWidth + parseFloat(style.marginRight || 0);
            }

            function showSlide(index) {
                if (!slides || slides.length === 0) return;
                currentIndex = (index + slides.length) % slides.length;
                const cardWidth = getCardWidth();
                const offset = currentIndex * cardWidth;
                slider.scrollTo({ left: offset, behavior: 'smooth' });
            }

            function next() { showSlide(currentIndex + 1); }

            // Make cards clickable
            slides?.forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => {
                    window.location.href = 'activiteiten.php';
                });
            });

            // Auto-play every 7 seconds
            setInterval(next, 7000);

            window.addEventListener('resize', () => showSlide(currentIndex));
            showSlide(0);
        })();
    </script>

    <!-- Footer -->
    <div class="footer"></div>

    <!-- Load JS modules -->
    <script type="module" src="js/main.js"></script>

</body>

</html>