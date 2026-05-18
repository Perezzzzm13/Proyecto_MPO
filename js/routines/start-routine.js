document.addEventListener('DOMContentLoaded', iniciarSesionRutina);

function iniciarSesionRutina() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');

    const contenedorMensaje = document.getElementById('mensaje-rutina');
    const btnVolverRutina = document.getElementById('btn-volver-rutina');
    const formularioSesion = document.getElementById('form-sesion-rutina');

    if (!idRutina) {
        contenedorMensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    btnVolverRutina.href = `routine-view.html?id=${idRutina}`;

    formularioSesion.addEventListener('submit', function(event) {
        guardarSesion(event, idRutina);   
    });

    cargarRutinaParaSesion(idRutina, contenedorMensaje);
}

async function cargarRutinaParaSesion(idRutina, contenedorMensaje) {
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
            listaEjercicios.innerHTML = '<p>Esta rutina no tiene ejercicios. Añade ejercicios antes de iniciarla.</p>';
            return;
        }

        pintarEjerciciosSesion(data.ejercicios, listaEjercicios);

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar la rutina.';
    }
}

function pintarEjerciciosSesion(ejercicios, listaEjercicios) {
    let html = '';

    for (const ejercicio of ejercicios) {
        html += `
            <article class="card-ejercicio">
                <h2>${ejercicio.orden}. ${ejercicio.nombre}</h2>

                <input type="hidden" name="id_ejercicio[]" value="${ejercicio.id_ejercicio}">

                <div class="campo-formulario">
                    <label>Serie</label>
                    <input 
                        type="number" 
                        name="numero_serie[]" 
                        value="1" 
                        min="1"
                        required
                    >
                </div>

                <div class="campo-formulario">
                    <label>Repeticiones</label>
                    <input 
                        type="number" 
                        name="repeticiones[]" 
                        min="0"
                        required
                    >
                </div>

                <div class="campo-formulario">
                    <label>Peso (kg)</label>
                    <input 
                        type="number" 
                        name="peso[]" 
                        min="0"
                        step="0.5"
                    >
                </div>
            </article>
        `;
    }

    listaEjercicios.innerHTML = html;
}

async function guardarSesion(event, idRutina) {
    event.preventDefault();

    const contenedorMensaje = document.getElementById('mensaje-rutina');

    const idsEjercicios = document.getElementsByName('id_ejercicio[]');
    const numerosSerie = document.getElementsByName('numero_serie[]');
    const repeticiones = document.getElementsByName('repeticiones[]');
    const pesos = document.getElementsByName('peso[]');

    const ejercicios = [];

    for (let i = 0; i < idsEjercicios.length; i++) {
        ejercicios.push({
            id_ejercicio: idsEjercicios[i].value,
            numero_serie: numerosSerie[i].value,
            repeticiones: repeticiones[i].value,
            peso: pesos[i].value
        });
    }

    const datosSesion = {
        id_rutina: idRutina,
        ejercicios: ejercicios
    };

    contenedorMensaje.textContent = '';

    try {
        const respuesta = await fetch('../../backend/sessions/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosSesion)
        });

        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        window.location.href = `routine-view.html?id=${idRutina}`;

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al guardar la sesión.';
    }
}