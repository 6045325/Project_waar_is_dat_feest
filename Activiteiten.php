
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

<body id="Activiteiten-page">
    <?php require_once 'includes/navbar.php'; ?>
<!-- FORMULIER -->
<div class="form-container">
    <h2>Nieuwe activiteit</h2>

    <form id="activiteitForm">
        <input type="text" id="naam" placeholder="Naam activiteit" required>
        <input type="text" id="adres" placeholder="Adres" required>
        <input type="date" id="datum" required>
        <input type="text" id="tijden" placeholder="10:00 - 14:00" required>
        <input type="text" id="tags" placeholder="Tags (komma gescheiden)">
        <input type="text" id="foto" placeholder="Afbeelding URL">

        <button type="submit">Opslaan</button>
    </form>
</div>

<!-- KAARTEN -->
<div class="activiteiten-container" id="activiteitenContainer"></div>


<script>
const form = document.getElementById("activiteitForm");
const container = document.getElementById("activiteitenContainer");

let activiteiten = JSON.parse(localStorage.getItem("activiteiten")) || [];
let editIndex = null;

/* OPSLAAN */
function saveToStorage() {
    localStorage.setItem("activiteiten", JSON.stringify(activiteiten));
}

/* KAART MAKEN */
function maakKaart(data, index) {
    const kaart = document.createElement("div");
    kaart.classList.add("activiteit-kaart");

    const formattedDate = new Date(data.datum).toLocaleDateString('nl-NL');

    kaart.innerHTML = `
        <img src="${data.foto || 'https://via.placeholder.com/180'}">

        <div class="kaart-info">
            <div class="kaart-header">
                <div>
                    <h2>${data.naam}</h2>
                    <p class="adres">${data.adres}</p>
                </div>
                <div>
                    <p class="datum">${formattedDate}</p>
                    <button class="delete-btn">Delete</button>
                    <button class="edit-btn">✏️</button>
                </div>
            </div>

            <div class="kaart-tags">
                ${data.tags.map(tag => `<span>${tag}</span>`).join('')}
            </div>

            <p class="tijden">${data.tijden}</p>
        </div>
    `;

    // DELETE
    kaart.querySelector(".delete-btn").onclick = () => {
        if (!confirm("Weet je het zeker?")) return;

        activiteiten.splice(index, 1);
        saveToStorage();
        renderAlles();
    };

    // EDIT
    kaart.querySelector(".edit-btn").onclick = () => {
        document.getElementById("naam").value = data.naam;
        document.getElementById("adres").value = data.adres;
        document.getElementById("datum").value = data.datum;
        document.getElementById("tijden").value = data.tijden;
        document.getElementById("tags").value = data.tags.join(', ');
        document.getElementById("foto").value = data.foto;

        editIndex = index;
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    return kaart;
}

/* RENDER */
function renderAlles() {
    container.innerHTML = "";
    activiteiten.forEach((item, index) => {
        container.appendChild(maakKaart(item, index));
    });
}

/* SUBMIT */
form.addEventListener("submit", function(e) {
    e.preventDefault();

    const data = {
        naam: document.getElementById("naam").value,
        adres: document.getElementById("adres").value,
        datum: document.getElementById("datum").value,
        tijden: document.getElementById("tijden").value,
        tags: document.getElementById("tags").value
            ? document.getElementById("tags").value.split(',').map(t => t.trim())
            : [],
        foto: document.getElementById("foto").value
    };

    if (!data.naam || !data.adres) {
        alert("Vul minimaal naam en adres in!");
        return;
    }

    if (editIndex !== null) {
        activiteiten[editIndex] = data;
        editIndex = null;
    } else {
        activiteiten.push(data);
    }

    saveToStorage();
    renderAlles();
    form.reset();
});

/* INIT */
renderAlles();
</script>

</body>
</html>