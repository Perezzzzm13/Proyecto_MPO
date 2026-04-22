document.addEventListener('DOMContentLoaded', iniciarVistaRutina);

function iniciarVistaRutina() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');
    const contenedorMensaje = document.getElementById('mensaje-rutina');

    if (!idRutina) {
        contenedorMensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    cargarRutinas(idRutina, contenedorMensaje);
}

async function cargarRutina(idRutina, contenedorMensaje) {
    const nombreRutina = document.getElementById('nombre-rutina');
    const descripcionRutina = document.getElementById('descripcion-rutina');
    const listaEjercicios = document.getElementById('lista-ejercicios');

    contenedorMensaje.textContent = '';
    nombreRutina.textContent = '';
    descripcionRutina.textContent = '';
    listaEjercicios.innerHTML = '';
    
    try {
        const respuesta = await fetch(`../../backend/routines/get.php?id=${idRutina}`);
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        nombreRutina.textContent = data.rutina.nombre;
        descripcionRutina.textContent = data.rutina.descripcion ? data.rutina.descripcion : 'Sin descripción';

        if (data.ejercicios.length === 0) {
            listaEjercicios.innerHTML = '<p>Esta rutina todavía no tiene ejercicios.</p>';
            return;
        }

        pintarEjercicios(data.ejercicios, listaEjercicios);

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar la rutina.';
    }
}

function pintarEjercicios(ejercicios, listaEjercicios) {
    let html = '';

    for (const ejercicio of ejercicios) {
        html += `
            <article class="card-ejercicio">
                <h3>${ejercicio.orden}. ${ejercicio.nombre}</h3>
            </article>
        `;
    }

    listaEjercicios.innerHTML = html;
}