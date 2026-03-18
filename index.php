<?php
$title = "welcome";

$activiteiten = [
    [
        "title" => "Surfing",
        "image" => "images/placeholder.jpg",
        "text"  => "Learn how to surf on the beautiful beach."
    ],
    [
        "title" => "Beach Volleyball",
        "image" => "images/placeholder.jpg",
        "text"  => "Play volleyball with friends on the sand."
    ],
    [
        "title" => "Boat Tour",
        "image" => "images/placeholder.jpg",
        "text"  => "Enjoy a relaxing boat tour on the water."
    ],
    [
        "title" => "Cycling",
        "image" => "images/placeholder.jpg",
        "text"  => "Explore the dunes with a cycling route."
    ]
];
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
        <div class="card-grid">

            <?php foreach ($activiteiten as $activiteit): ?>

                <div class="card">
                    <img src="<?php echo $activiteit['image']; ?>" alt="<?php echo $activiteit['title']; ?>">

                    <div class="card-content">
                        <h3><?php echo $activiteit['title']; ?></h3>
                        <p><?php echo $activiteit['text']; ?></p>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>

    <!-- Footer -->
    <div class="footer"></div>

</body>

</html>