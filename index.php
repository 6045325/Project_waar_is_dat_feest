
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body id="Index-page">

<!-- Navbar -->
<div class="navbar">
<h1><a href="index.php">Homepage</a></h1>   
<a href="login/login.php">
    <div class="nav-circle">
        <img src="images/logged_out.jpg" alt="profile">
    </div>
</a>
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

        <?php foreach($activiteiten as $activiteit): ?>

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
