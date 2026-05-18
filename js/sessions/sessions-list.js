document.addEventListener('DOMContentLoaded', iniciarPaginaSesiones);

function iniciarPaginaSesiones() {
    const contenedorMensaje = document.getElementById('mensaje-sesiones');
    const contenedorSesiones = document.getElementById('lista-sesiones');

    cargarSesiones(contenedorMensaje, contenedorSesiones);
}

async function cargarSesiones(contenedorMensaje, contenedorSesiones) {
    contenedorMensaje.textContent = '';
    contenedorSesiones.innerHTML = '';

    try {
        const respuesta = await fetch('../../backend/sessions/list.php');
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        if (data.sesiones.length === 0) {
            contenedorMensaje.textContent = 'Todavía no tienes sesiones guardadas.';
            return;
        }

        pintarSesiones(data.sesiones, contenedorSesiones);

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar las sesiones.';
    }
}

function pintarSesiones(sesiones, contenedorSesiones) {
    let html = '';

    for (const sesion of sesiones) {
        html += `
            <article class="card-sesion">
                <h2>${sesion.nombre_rutina}</h2>
                <p><strong>Fecha:</strong> ${sesion.fecha_hora}</p>

                <a href="session-detail.html?id=${sesion.id_sesion}" class="btn btn-primary">
                    Ver detalle
                </a>
            </article>
        `;
    }

    contenedorSesiones.innerHTML = html;
}