<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Jost', sans-serif;
        }

        #form {
            width: 350px;
            height: 500px;
            background: #0b1f3a;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 5px 20px 50px #000;
        }

        #chk {
            display: none;
        }

        .login {
            position: relative;
            width: 100%;
            height: 100%;
        }

        label {
            color: #ffffff;
            font-size: 2.3em;
            justify-content: center;
            display: flex;
            margin: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: .5s ease-in-out;
        }

        input {
            width: 60%;
            height: 10px;
            background: #ffffff;
            justify-content: center;
            display: flex;
            margin: 20px auto;
            padding: 12px;
            border: none;
            outline: none;
            border-radius: 5px;
        }

        button {
            width: 60%;
            height: 40px;
            margin: 10px auto;
            justify-content: center;
            display: block;
            color: #fff;
            background-color: #B59410;
            font-size: 1em;
            font-weight: bold;
            margin-top: 30px;
            outline: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #b8860b;
            /* iets lichtere goudkleur */
            transition: background-color 0.3s ease;
        }

        .signup {
            height: 460px;
            background: #2a2f45;
            border-radius: 60% / 10%;
            transform: translateY(-160px);
            transition: .8s ease-in-out;
        }

        .signup label {
            color: #ffffff;
            transform: scale(.6);
        }

        #chk:checked~.signup {
            transform: translateY(-490px);
        }

        #chk:checked~.signup label {
            transform: scale(1);
        }

        #chk:checked~.login label {
            transform: scale(.6);
        }

        .error-message {
            color: #ff4d4d;
            /* Een iets fellere rood */
            margin-top: 5px;
            font-size: 0.9em;
            padding-left: 5px;
            /* Iets minder padding omdat het icoontje wat ruimte inneemt */
        }

        .error-icon {
            color: #ff4d4d;
            font-size: 1em;
            /* Dezelfde grootte als de tekst */
            margin-right: 5px;
            /* Wat ruimte tussen het icoon en de tekst */
        }

        body {
            background-color: #f0f0f0;
            background-image: url("img/background.png");
            background-size: cover;
        }
    </style>
</head>

<body>

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
                        window.location.href = "index.php";
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