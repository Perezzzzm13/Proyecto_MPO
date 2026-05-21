document.addEventListener('DOMContentLoaded', iniciarFormularioRutina);

function iniciarFormularioRutina() {
    const formulario = document.getElementById('form-crear-rutina');

    formulario.addEventListener('submit', enviarFormularioRutina);
}

async function enviarFormularioRutina(evento) {
    evento.preventDefault();

    const inputNombre = document.getElementById('nombre');
    const inputDescripcion = document.getElementById('descripcion');
    const contenedorMensaje = document.getElementById('mensaje-rutina');

    const nombre = inputNombre.value.trim();
    const descripcion = inputDescripcion.value.trim();

    contenedorMensaje.textContent = '';

    if (!nombre) {
        contenedorMensaje.textContent = 'El nombre de la rutina es obligatorio.';
        return;
    }

    const datosRutina = {
        nombre: nombre,
        descripcion: descripcion
    };

    try {
        const respuesta = await fetch('../../backend/routines/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosRutina)
        });

        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        window.location.href = 'routines.php';

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al crear la rutina.';
    }
}