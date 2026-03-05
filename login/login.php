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

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {

            e.preventDefault(); // voorkomt reload

            const formData = new FormData(this);

            fetch("api/login.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {
                        window.location.href = "../dashboard.php"; // Redirect naar dashboard
                    } else {
                        document.getElementById("loginError").innerText = data.message;
                    }

                })
                .catch(error => {
                    console.error(error);
                });

        });


        document.getElementById("signupForm").addEventListener("submit", function(e) {

            e.preventDefault();

            const formData = new FormData(this);
            fetch("api/signup.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("loginError").style.color = "lightgreen";
                        document.getElementById("loginError").innerText = data.message;
                        document.getElementById("chk").checked = false; // false = login
                    } else {
                        document.getElementById("signupError").style.color = "red";
                        document.getElementById("signupError").innerText = data.message;
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                });

        });
    </script>

</body>

</html>