<?php
$title = "welcome";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activiteiten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">
</head>

<body id="activiteiten-page">

<!-- Navbar -->
<div class="navbar">
    <h1>Activiteiten</h1>
    <div class="nav-circle"></div>
</div>

<!-- Hero -->
<div class="hero">
    <h2><?php echo $title; ?></h2>
</div>

<!-- Content -->
<div class="content">
    <p>
        Lorem ipsum dolor sit amet consectetur. Pellentesque aliquet sed 
        ullamcorper etiam sit. Magnis lectus lacus laoreet dignissim rutrum. 
        Gravida eget velit pharetra nulla aliquam interdum eu justo placerat. 
        Elementum eget cursus enim quis.
    </p>
</div>

<!-- Cards -->
<div class="card-section">
    <div class="card-grid">

        <?php for($i = 0; $i < 4; $i++): ?>
            <div class="card">
                <img src="images/placeholder.jpg" alt="placeholder image">
                <div class="card-content">
                    <h3>Activiteit <?php echo $i + 1; ?></h3>
                </div>
            </div>
        <?php endfor; ?>

    </div>
</div>

<!-- Footer -->
<div class="footer"></div>

</body>
</html>