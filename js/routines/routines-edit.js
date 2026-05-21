document.addEventListener('DOMContentLoaded', iniciarEdicionRutina);

function iniciarEdicionRutina() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');
    const mensaje = document.getElementById('mensaje-rutina');
    const formulario = document.getElementById('form-editar-rutina');
    const btnVolver = document.getElementById('btn-volver-rutina');

    if (!idRutina) {
        mensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    btnVolver.href = `routine-view.php?id=${idRutina}`;

    formulario.addEventListener('submit', function(evento) {
        guardarCambiosRutina(evento, idRutina);
    });

    cargarDatosRutina(idRutina, mensaje);
}

async function cargarDatosRutina(idRutina, mensaje) {
    const inputNombre = document.getElementById('nombre');
    const inputDescripcion = document.getElementById('descripcion');

    try {
        const respuesta = await fetch(`../../backend/routines/get.php?id=${idRutina}`);
        const data = await respuesta.json();

        if (!data.success) {
            mensaje.textContent = data.message;
            return;
        }

        inputNombre.value = data.rutina.nombre;
        inputDescripcion.value = data.rutina.descripcion || '';
    } catch (error) {
        mensaje.textContent = 'Ha ocurrido un error al cargar la rutina.';
    }
}

async function guardarCambiosRutina(evento, idRutina) {
    evento.preventDefault();

    const inputNombre = document.getElementById('nombre');
    const inputDescripcion = document.getElementById('descripcion');
    const mensaje = document.getElementById('mensaje-rutina');

    const nombre = inputNombre.value.trim();
    const descripcion = inputDescripcion.value.trim();

    mensaje.textContent = '';

    if (!nombre) {
        mensaje.textContent = 'El nombre de la rutina es obligatorio.';
        return;
    }

    try {
        const respuesta = await fetch('../../backend/routines/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_rutina: idRutina,
                nombre: nombre,
                descripcion: descripcion
            })
        });

        const data = await respuesta.json();

        if (!data.success) {
            mensaje.textContent = data.message;
            return;
        }

        window.location.href = `routine-view.php?id=${idRutina}`;
    } catch (error) {
        mensaje.textContent = 'Ha ocurrido un error al guardar los cambios.';
    }
}
