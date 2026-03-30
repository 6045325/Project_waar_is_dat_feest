import Auth from "./auth.js";

export default class Forms {

    constructor() {
        this.loginForm = document.getElementById("loginForm");
        this.signupForm = document.getElementById("signupForm");

        this.loginError = document.getElementById("loginError");
        this.signupError = document.getElementById("signupError");

        this.start();
    }

    start() {

        if (this.loginForm) {
            this.loginForm.addEventListener("submit", (e) => this.login(e));
        }

        if (this.signupForm) {
            this.signupForm.addEventListener("submit", (e) => this.signup(e));
        }

    }

    async login(e) {

        e.preventDefault();

        const formData = new FormData(this.loginForm);
        const data = await Auth.login(formData);

        if (data.success) {
            window.location.href = "index.php";
        } else {
            this.loginError.innerText = data.message;
        }

    }

    async signup(e) {

        e.preventDefault();

        const formData = new FormData(this.signupForm);
        const data = await Auth.signup(formData);

        if (data.success) {

            this.signupError.style.color = "lightgreen";
            this.signupError.innerText = data.message || "Account succesvol aangemaakt.";
            document.getElementById("chk").checked = false;

            this.signupForm.reset();

        } else {

            this.signupError.style.color = "red";
            this.signupError.innerText = data.message;

        }

    }

}