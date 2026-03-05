<?php
// You can later make this dynamic if needed
$title = "welcome";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activiteiten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #CAD2C5;
            color: #2F3E46;
        }

        /* Header */
        .navbar {
            background-color: rgba(47, 62, 70, 0.7); /* 0.8 = 80% opacity */
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #CAD2C5;
            position: fixed; /* optional, if you want fixed navbar */
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar h1 {
            font-size: 20px;
        }

        .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000; /* ensure it stays above hero */
        }

        .nav-circle {
            width: 35px;
            height: 35px;
            background-color: #52796F;
            border-radius: 50%;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh; /* full viewport height */
            margin-top: 0;  /* make sure it starts under navbar */
            background: linear-gradient(rgba(37,79,82,0.7), rgba(37,79,82,0.7)),
                        url("images/placeholder.jpg") center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #CAD2C5;
        }

        .hero h2 {
            font-size: 48px;
            text-transform: lowercase;
        }

        /* Content Section */
        .content {
            padding: 40px 20px;
            text-align: center;
            max-width: 900px;
            margin: auto;
        }

        .content p {
            margin-top: 20px;
            margin-bottom: 0px;
            line-height: 1.6;
        }

        /* Card Section */
        .card-section {
            background-color: #84A98C;
            padding: 40px 20px;
        }

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* flexible columns */
    gap: 30px;
    max-width: 1100px;
    margin: auto;
}

/* Limit to max 2 columns */
@media (min-width: 600px) {
    .card-grid {
        grid-template-columns: repeat(2, 1fr); /* max 2 columns on larger screens */
    }
}

/* Make it single column on small screens */
@media (max-width: 599px) {
    .card-grid {
        grid-template-columns: 1fr;
    }
}

        .card {
            background-color: #52796F;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            display: block;
        }

        .card-content {
            padding: 15px;
            color: #CAD2C5;
            text-align: center;
        }

        /* Footer */
        .footer {
            background-color: #2F3E46;
            height: 80px;
            margin-top: 40px;
        }

        @media (max-width: 600px) {
            .hero h2 {
                font-size: 32px;
            }
        }

    </style>
</head>

<body>

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