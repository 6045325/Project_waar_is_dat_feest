
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    <div id="Index-page">
        <?php require_once 'includes/navbar.php'; ?>
    </div>

    <div class="container">
    <h1>Activiteiten</h1>
    <div id="activiteiten-lijst" class="cards"></div>
</div>

<script>
fetch('get_activiteiten.php')
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('activiteiten-lijst');

        data.forEach(a => {
            const card = document.createElement('div');
            card.classList.add('card');

            card.innerHTML = `
                <div class="card-header">
                    <h2>${a.activiteit_titel}</h2>
                    <span class="status ${a.activiteit_status}">${a.activiteit_status}</span>
                </div>

                <p class="beschrijving">${a.activiteit_beschrijving}</p>

                <div class="card-info">
                    <p><strong> Datum:</strong> ${a.activiteit_datum}</p>
                    <p><strong> Tijd:</strong> ${a.activiteit_tijd}</p>
                    <p><strong> Locatie:</strong> ${a.activiteit_locatie}</p>
                    <p><strong> Soort:</strong> ${a.soort_activiteit}</p>
                </div>

                <div class="card-footer">
                    <small>${a.activiteit_opmerkingen ?? ''}</small>
                </div>
            `;

            container.appendChild(card);
        });
    });
</script>
</body>
</html>

