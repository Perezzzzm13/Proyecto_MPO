
document.addEventListener('DOMContentLoaded', iniciarPaginaRutinas);

function iniciarPaginaRutinas() {
    const contenedorMensaje = document.getElementById('mensaje-rutinas');
    const contenedorRutinas = document.getElementById('lista-rutinas');

    cargarRutinas(contenedorMensaje, contenedorRutinas);
}

async function cargarRutinas(contenedorMensaje, contenedorRutinas) {
    
    //Limpiar mensajes para que no queden restos al recargar.
    contenedorMensaje.textContent = '';
    contenedorRutinas.innerHTML = '';

    try {
        const respuesta = await fetch('../../backend/routines/list.php');
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        if (data.rutinas.length === 0) {
            contenedorMensaje.textContent = 'Todavía no tienes rutinas creadas';
            return;
        }

        pintarRutinas(data.rutinas, contenedorRutinas);
    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar las rutinas';
    }
}

function pintarRutinas(rutinas, contenedorRutinas) {
    let html = '';

    for (const rutina of rutinas) {
        html += `
            <article class="card-rutina">
                <h2>${rutina.nombre}</h2>
                <p>${rutina.descripcion ? rutina.descripcion : 'Sin descripción'}</p>
                <a href="routine-detail.html?id=${rutina.id_rutina}" class="btn">Ver rutina</a>
            </article>
        `;
    }

    contenedorRutinas.innerHTML = html;
}