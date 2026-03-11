export default class Auth {

    static async login(formData) {
        return this.post("api/login.php", formData);
    }

    static async signup(formData) {
        return this.post("api/signup.php", formData);
    }

    static async post(url, formData) {

        try {
            const response = await fetch(url, {
                method: "POST",
                body: formData
            });

            return await response.json();

        } catch (error) {
            console.error(error);

            return {
                success: false,
                message: "Server error"
            };
        }

    }
}