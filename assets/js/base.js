document.addEventListener('DOMContentLoaded', function () {

    // ===== MODAL FLASH =====
    const flashModalElement = document.getElementById('flashModal');

    if (flashModalElement) {
        const flashModal = new bootstrap.Modal(flashModalElement);
        flashModal.show();
    }

    // ===== TOGGLE PASSWORD =====
    const toggle = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    if (toggle && password) {
        toggle.addEventListener("click", () => {
            const type = password.type === "password" ? "text" : "password";
            password.type = type;

            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        });
    }

});