<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>
    <link rel="stylesheet" href="../css/style.css">
    <script type="module" src="js/main.js"></script>
    <style>

    </style>
</head>

<body id="login">

    <section id="form">
        <input type="checkbox" id="chk">

        <article class="login">
            <form id="loginForm">
                <label for="chk">Log in</label>
                <input type="text" name="username" placeholder="username" required>
                <input type="password" name="password" placeholder="password" required>
                <button type="submit">Log in</button>
                <p class="error-message" id="loginError"></p>
            </form>
        </article>

        <article class="signup">
            <form id="signupForm">
                <label for="chk">Sign up</label>
                <input type="text" name="username" placeholder="username" required>
                <input type="password" name="password" placeholder="password" required>
                <input type="password" name="confirm_password" placeholder="confirm password" required>
                <button type="submit">Create account</button>
                <p class="error-message" id="signupError"></p>
            </form>
        </article>
    </section>
</body>

</html>