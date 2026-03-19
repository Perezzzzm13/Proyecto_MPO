const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value;

    clearMessage(loginMessage);

    if (email === "" || password === "") {
        showMessage(loginMessage, "Debes completar todos los campos.", "error");
        return;
    }

    if (!isValidEmail(email)) {
        showMessage(loginMessage, "Introduce un correo electrónico válido.", "error");
        return;
    }

    const loginData = {
        email: email,
        password: password
    };

    try {
        const response = await fetch("../../backend/auth/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(loginData)
        });

        const data = await response.json();

        if (data.success) {
            showMessage(loginMessage, data.message, "success");

            setTimeout(function () {
                window.location.href = "../dashboard/dashboard.html";
            }, 1000);
        } else {
            showMessage(loginMessage, data.message, "error");
        }
    } catch (error) {
        showMessage(loginMessage, "Ha ocurrido un error al iniciar sesión.", "error");
        console.error("Error en login:", error);
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