<?php
session_start();
if (!empty($_SESSION['logged_in'])) {
    header('Location: ../dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body id="login">
    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-header">
                <p class="auth-kicker">Welkom terug</p>
                <h1>Activiteitenplatform</h1>
                <p>Log in of maak een account aan in dezelfde stijl als je activiteitenpagina.</p>
            </div>

            <div class="auth-switch" role="tablist" aria-label="Login of signup">
                <button type="button" class="auth-switch-btn active" data-auth-target="login-panel">Log in</button>
                <button type="button" class="auth-switch-btn" data-auth-target="signup-panel">Sign up</button>
            </div>

            <section class="auth-panel active" id="login-panel">
                <form id="loginForm" class="auth-form">
                    <label for="login-username">Gebruikersnaam</label>
                    <input id="login-username" type="text" name="username" placeholder="Gebruikersnaam" required>

                    <label for="login-password">Wachtwoord</label>
                    <input id="login-password" type="password" name="password" placeholder="Wachtwoord" required>

                    <button type="submit" class="auth-submit">Log in</button>
                    <p class="error-message" id="loginError"></p>
                </form>
            </section>

            <section class="auth-panel" id="signup-panel" hidden>
                <form id="signupForm" class="auth-form">
                    <label for="signup-username">Gebruikersnaam</label>
                    <input id="signup-username" type="text" name="username" placeholder="Gebruikersnaam" required>

                    <label for="signup-email">E-mailadres</label>
                    <input id="signup-email" type="email" name="email" placeholder="E-mailadres" required>

                    <label for="signup-password">Wachtwoord</label>
                    <input id="signup-password" type="password" name="password" placeholder="Wachtwoord" required>

                    <label for="signup-confirm">Herhaal wachtwoord</label>
                    <input id="signup-confirm" type="password" name="confirm_password" placeholder="Herhaal wachtwoord" required>

                    <button type="submit" class="auth-submit">Account aanmaken</button>
                    <p class="error-message" id="signupError"></p>
                </form>
            </section>
        </div>
    </div>

    <script>
        const switchButtons = document.querySelectorAll('.auth-switch-btn');
        const panels = document.querySelectorAll('.auth-panel');

        switchButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.authTarget;
                switchButtons.forEach(btn => btn.classList.toggle('active', btn === button));
                panels.forEach(panel => {
                    const active = panel.id === targetId;
                    panel.classList.toggle('active', active);
                    panel.hidden = !active;
                });
            });
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api/login.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../dashboard.php';
                    } else {
                        const loginError = document.getElementById('loginError');
                        loginError.style.color = '#d9534f';
                        loginError.innerText = data.message;
                    }
                })
                .catch(() => {
                    const loginError = document.getElementById('loginError');
                    loginError.style.color = '#d9534f';
                    loginError.innerText = 'Er ging iets mis bij het inloggen.';
                });
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api/signup.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const loginBtn = document.querySelector('[data-auth-target="login-panel"]');
                        loginBtn.click();
                        const loginError = document.getElementById('loginError');
                        loginError.style.color = '#4fa56a';
                        loginError.innerText = data.message;
                        document.getElementById('signupError').innerText = '';
                        this.reset();
                    } else {
                        const signupError = document.getElementById('signupError');
                        signupError.style.color = '#d9534f';
                        signupError.innerText = data.message;
                    }
                })
                .catch(() => {
                    const signupError = document.getElementById('signupError');
                    signupError.style.color = '#d9534f';
                    signupError.innerText = 'Er ging iets mis bij het registreren.';
                });
        });
    </script>
</body>
</html>
