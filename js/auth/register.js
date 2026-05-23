const registerForm = document.getElementById("registerForm");
const registerMessage = document.getElementById("registerMessage");

registerForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const nombreUsuario = document.getElementById("registerUsername").value.trim();
    const nombre = document.getElementById("registerName").value.trim();
    const apellidos = document.getElementById("registerSurname").value.trim();
    const email = document.getElementById("registerEmail").value.trim();
    const password = document.getElementById("registerPassword").value;
    const confirmPassword = document.getElementById("registerConfirmPassword").value;

    clearMessage(registerMessage);

    if (
        nombreUsuario === "" ||
        nombre === "" ||
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
        nombre_usuario: nombreUsuario,
        nombre: nombre,
        apellidos: apellidos,
        email: email,
        password: password
    };

    try {
        
        const response = await fetch("../../backend/auth/register.php", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(userData)
        });
        console.log(userData);

        const data = await response.json();

        if (data.success) {
            showMessage(registerMessage, data.message, "exito");
            registerForm.reset();

            setTimeout(function () {
                window.location.href = "../dashboard/dashboard.php";
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
    element.classList.remove("exito", "error");
    element.classList.add(type);
}

function clearMessage(element) {
    element.textContent = "";
    element.classList.remove("exito", "error");
}
