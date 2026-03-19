const registerForm = document.getElementById("registerForm");
const registerMessage = document.getElementById("registerMessage");

registerForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const username = document.getElementById("registerUsername").value.trim();
    const name = document.getElementById("registerName").value.trim();
    const surname = document.getElementById("registerSurname").value.trim();
    const email = document.getElementById("registerEmail").value.trim();
    const password = document.getElementById("registerPassword").value;
    const confirmPassword = document.getElementById("registerConfirmPassword").value;

    clearMessage(registerMessage);

    if (
        username === "" ||
        name === "" ||
        email === "" ||
        password === "" ||
        confirmPassword === ""
    ) {
        showMessage(registerMessage, "Todos los campos obligatorios deben estar rellenos.", "error");
        return;
    }

    if (!isValidEmail(email)) {
        showMessage(registerMessage, "Introduce un correo electrónico válido.", "error");
        return;
    }

    if (password.length < 6) {
        showMessage(registerMessage, "La contraseña debe tener al menos 6 caracteres.", "error");
        return;
    }

    if (password !== confirmPassword) {
        showMessage(registerMessage, "Las contraseñas no coinciden.", "error");
        return;
    }

    const userData = {
        nombre_usuario: username,
        nombre: name,
        apellidos: surname,
        email: email,
        password: password
    };

    try {
        const response = await fetch("../../backend/auth/register.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();

        if (data.success) {
            showMessage(registerMessage, data.message, "success");
            registerForm.reset();

            setTimeout(function () {
                window.location.href = "./login.html";
            }, 1200);
        } else {
            showMessage(registerMessage, data.message, "error");
        }
    } catch (error) {
        showMessage(registerMessage, "Ha ocurrido un error al procesar el registro.", "error");
        console.error("Error en registro:", error);
    }
});

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showMessage(element, message, type) {
    element.textContent = message;
    element.classList.remove("success", "error");
    element.classList.add(type);
}

function clearMessage(element) {
    element.textContent = "";
    element.classList.remove("success", "error");
}