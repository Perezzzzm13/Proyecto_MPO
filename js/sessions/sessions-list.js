document.addEventListener('DOMContentLoaded', iniciarPaginaSesiones);

function iniciarPaginaSesiones() {
    const contenedorMensaje = document.getElementById('mensaje-sesiones');
    const contenedorSesiones = document.getElementById('lista-sesiones');

    cargarSesiones(contenedorMensaje, contenedorSesiones);

    const btnCerrarModal = document.getElementById('cerrar-modal-sesion');
    btnCerrarModal.addEventListener('click', cerrarModalSesion);

    const btnCerrarEstadisticas = document.getElementById('cerrar-modal-estadisticas');
    btnCerrarEstadisticas.addEventListener('click', cerrarModalEstadisticas);
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
            contenedorMensaje.textContent = 'Todavia no tienes sesiones guardadas.';
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
        const puedeVerEstadisticas = Number(sesion.tiene_sesion_anterior) === 1;

        html += `
            <article class="card-sesion">
                <div>
                    <span class="card-meta">Sesion guardada</span>
                    <h2>${sesion.nombre_rutina}</h2>
                    <p><strong>Fecha:</strong> ${formatearFechaSesion(sesion.fecha_hora)}</p>
                </div>

                <div class="acciones-card-sesion">
                    <button
                        type="button"
                        class="btn btn-primary btn-ver-sesion"
                        data-id="${sesion.id_sesion}"
                    >
                        Ver detalle
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary btn-ver-estadisticas"
                        data-id="${sesion.id_sesion}"
                        ${puedeVerEstadisticas ? '' : 'disabled'}
                    >
                        ${puedeVerEstadisticas ? 'Ver estadisticas' : 'Sin comparativa'}
                    </button>
                </div>
            </article>
        `;
    }

    contenedorSesiones.innerHTML = html;

    const botonesVerSesion = document.querySelectorAll('.btn-ver-sesion');

    for (const btn of botonesVerSesion) {
        btn.addEventListener('click', abrirModalSesion);
    }

    const botonesVerEstadisticas = document.querySelectorAll('.btn-ver-estadisticas');

    for (const btn of botonesVerEstadisticas) {
        if (!btn.disabled) {
            btn.addEventListener('click', abrirModalEstadisticas);
        }
    }
}

async function abrirModalSesion(evento) {
    const idSesion = evento.target.dataset.id;

    const modal = document.getElementById('modal-sesion');
    const nombreRutina = document.getElementById('modal-nombre-rutina');
    const fechaSesion = document.getElementById('modal-fecha-sesion');
    const detalleEjercicios = document.getElementById('modal-detalle-ejercicios');

    nombreRutina.textContent = 'Cargando sesion...';
    fechaSesion.textContent = '';
    detalleEjercicios.innerHTML = '';

    modal.classList.remove('oculto');

    try {
        const respuesta = await fetch(`../../backend/sessions/get.php?id=${idSesion}`);
        const data = await respuesta.json();

        if (!data.success) {
            nombreRutina.textContent = 'Error';
            detalleEjercicios.innerHTML = `<p class="rutina-vacia">${data.message}</p>`;
            return;
        }

        nombreRutina.textContent = data.sesion.nombre_rutina;
        fechaSesion.textContent = formatearFechaSesion(data.sesion.fecha_hora);

        pintarDetalleSesion(data.ejercicios, detalleEjercicios);
    } catch (error) {
        nombreRutina.textContent = 'Error';
        detalleEjercicios.innerHTML = '<p class="rutina-vacia">Ha ocurrido un error al cargar la sesion.</p>';
    }
}

function pintarDetalleSesion(ejercicios, contenedor) {
    let html = '';

    for (const ejercicio of ejercicios) {
        html += `
            <article class="card-ejercicio">
                <h3>${traducirEjercicio(ejercicio.nombre)}</h3>
                <div class="lista-detalle">
                    <p><strong>Serie:</strong> ${ejercicio.numero_serie}</p>
                    <p><strong>Repeticiones:</strong> ${ejercicio.repeticiones}</p>
                    <p><strong>Peso:</strong> ${ejercicio.peso} kg</p>
                </div>
            </article>
        `;
    }

    contenedor.innerHTML = html;
}

async function abrirModalEstadisticas(evento) {
    const idSesion = evento.target.dataset.id;

    const modal = document.getElementById('modal-estadisticas');
    const fechaActual = document.getElementById('modal-fecha-estadisticas');
    const fechaComparada = document.getElementById('modal-fecha-comparada');
    const contenido = document.getElementById('modal-contenido-estadisticas');

    fechaActual.textContent = 'Cargando estadisticas...';
    fechaComparada.textContent = '';
    contenido.innerHTML = '';

    modal.classList.remove('oculto');

    try {
        const respuesta = await fetch(`../../backend/sessions/stats.php?id=${idSesion}`);
        const data = await respuesta.json();

        if (!data.success) {
            fechaActual.textContent = 'No se pueden mostrar estadisticas';
            contenido.innerHTML = `<p class="rutina-vacia">${data.message}</p>`;
            return;
        }

        fechaActual.textContent = `Sesion actual: ${formatearFechaSesion(data.sesion_actual)}`;
        fechaComparada.textContent = `Comparada con: ${formatearFechaSesion(data.sesion_anterior)}`;

        pintarEstadisticas(data.estadisticas, contenido);
    } catch (error) {
        fechaActual.textContent = 'Error';
        contenido.innerHTML = '<p class="rutina-vacia">Ha ocurrido un error al cargar las estadisticas.</p>';
    }
}

function pintarEstadisticas(estadisticas, contenedor) {
    let html = `
        <table class="tabla-estadisticas">
            <thead>
                <tr>
                    <th>Ejercicio</th>
                    <th>Peso anterior</th>
                    <th>Peso actual</th>
                    <th>Progreso peso</th>
                    <th>Reps anteriores</th>
                    <th>Reps actuales</th>
                    <th>Progreso reps</th>
                </tr>
            </thead>
            <tbody>
    `;

    for (const estadistica of estadisticas) {
        let clasePeso = '';
        let textoPeso = '0';

        if (estadistica.progreso_peso > 0) {
            clasePeso = 'progreso-positivo';
            textoPeso = `+${estadistica.progreso_peso}`;
        } else if (estadistica.progreso_peso < 0) {
            clasePeso = 'progreso-negativo';
            textoPeso = estadistica.progreso_peso;
        }

        let claseReps = '';
        let textoReps = estadistica.mensaje_reps;

        if (!textoReps) {
            if (estadistica.progreso_reps > 0) {
                claseReps = 'progreso-positivo';
                textoReps = `+${estadistica.progreso_reps}`;
            } else if (estadistica.progreso_reps < 0) {
                claseReps = 'progreso-negativo';
                textoReps = estadistica.progreso_reps;
            } else {
                textoReps = '0';
            }
        }

        html += `
            <tr>
                <td>${estadistica.nombre}</td>
                <td>${estadistica.peso_anterior ?? '-'}</td>
                <td>${estadistica.peso_actual ?? '-'}</td>
                <td class="${clasePeso}">${textoPeso}</td>
                <td>${estadistica.reps_anteriores ?? '-'}</td>
                <td>${estadistica.reps_actuales ?? '-'}</td>
                <td class="${claseReps}">${textoReps}</td>
            </tr>
        `;
    }

    html += `
            </tbody>
        </table>
    `;

    contenedor.innerHTML = html;
}

function cerrarModalSesion() {
    const modal = document.getElementById('modal-sesion');
    modal.classList.add('oculto');
}

function cerrarModalEstadisticas() {
    const modal = document.getElementById('modal-estadisticas');
    modal.classList.add('oculto');
}

const traduccionesEjercicios = {
    'Rickshaw Carry': 'Paseo con rickshaw',
    'Single-Leg Press': 'Prensa a una pierna',
    'Landmine twist': 'Giro con barra landmine',
    'Dumbbell front raise to lateral raise': 'Elevacion frontal y lateral con mancuernas',
    'Palms-down wrist curl over bench': 'Curl de muneca prono en banco',
    'Atlas Stones': 'Levantamiento de piedras Atlas',
    'Clean from Blocks': 'Cargada desde bloques',
    'Incline Hammer Curls': 'Curl martillo inclinado',
    'Side Bridge': 'Plancha lateral',
    'Smith Machine Calf Raise': 'Elevacion de gemelos en maquina Smith',
    'Bench Press': 'Press de banca',
    'Pullups': 'Dominadas',
    'Triceps Pushdown': 'Extension de triceps en polea',
    'Leg Curl': 'Curl femoral',
    'Hip Thrust': 'Hip thrust',
};

function traducirEjercicio(nombre) {
    return traduccionesEjercicios[nombre] || nombre;
}

function formatearFechaSesion(fecha) {
    if (!fecha) {
        return 'Sin fecha';
    }

    const fechaNormalizada = String(fecha).replace(' ', 'T');
    const fechaSesion = new Date(fechaNormalizada);

    if (Number.isNaN(fechaSesion.getTime())) {
        return fecha;
    }

    return fechaSesion.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
