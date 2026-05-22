document.addEventListener('DOMContentLoaded', iniciarSesionRutina);

let ejerciciosRutina = [];
let seriesSesion = [];

function iniciarSesionRutina() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');

    const contenedorMensaje = document.getElementById('mensaje-rutina');
    const btnVolverRutina = document.getElementById('btn-volver-rutina');
    const formularioSesion = document.getElementById('form-sesion-rutina');
    const btnGuardarSerie = document.getElementById('btn-guardar-serie');

    if (!idRutina) {
        contenedorMensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    btnVolverRutina.href = `routine-view.php?id=${idRutina}`;

    btnGuardarSerie.addEventListener('click', guardarSerieTemporal);

    formularioSesion.addEventListener('submit', function(event) {
        guardarSesion(event, idRutina);
    });

    cargarRutinaParaSesion(idRutina, contenedorMensaje);
}

async function cargarRutinaParaSesion(idRutina, contenedorMensaje) {
    const nombreRutina = document.getElementById('nombre-rutina');
    const descripcionRutina = document.getElementById('descripcion-rutina');
    const selectEjercicio = document.getElementById('id-ejercicio');

    contenedorMensaje.textContent = '';
    nombreRutina.textContent = '';
    descripcionRutina.textContent = '';
    selectEjercicio.innerHTML = '<option value="">Selecciona ejercicio</option>';
    seriesSesion = [];
    pintarResumenSeries();

    try {
        const respuesta = await fetch(`../../backend/routines/get.php?id=${idRutina}`);
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        nombreRutina.textContent = data.rutina.nombre;
        descripcionRutina.textContent = data.rutina.descripcion ? data.rutina.descripcion : 'Sin descripción';
        ejerciciosRutina = data.ejercicios;

        if (ejerciciosRutina.length === 0) {
            document.getElementById('registro-series').innerHTML =
                '<p class="rutina-vacia">Esta rutina no tiene ejercicios. Añade ejercicios antes de iniciarla.</p>';
            return;
        }

        pintarOpcionesEjercicios(ejerciciosRutina, selectEjercicio);
    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar la rutina.';
    }
}

function pintarOpcionesEjercicios(ejercicios, selectEjercicio) {
    let html = '<option value="">Selecciona ejercicio</option>';

    for (const ejercicio of ejercicios) {
        html += `<option value="${ejercicio.id_ejercicio}">${ejercicio.orden}. ${traducirEjercicio(ejercicio.nombre)}</option>`;
    }

    selectEjercicio.innerHTML = html;
}

function guardarSerieTemporal() {
    const selectEjercicio = document.getElementById('id-ejercicio');
    const inputRepeticiones = document.getElementById('repeticiones-serie');
    const inputPeso = document.getElementById('peso-serie');
    const contenedorMensaje = document.getElementById('mensaje-rutina');

    const idEjercicio = selectEjercicio.value;
    const repeticiones = inputRepeticiones.value.trim();
    const peso = inputPeso.value.trim();

    contenedorMensaje.textContent = '';

    if (!idEjercicio) {
        contenedorMensaje.textContent = 'Selecciona un ejercicio antes de guardar la serie.';
        return;
    }

    if (repeticiones === '' || Number(repeticiones) < 0) {
        contenedorMensaje.textContent = 'Indica un número de repeticiones válido.';
        return;
    }

    if (peso !== '' && Number(peso) < 0) {
        contenedorMensaje.textContent = 'El peso no puede ser negativo.';
        return;
    }

    const ejercicio = ejerciciosRutina.find(function(ejercicioRutina) {
        return String(ejercicioRutina.id_ejercicio) === String(idEjercicio);
    });

    if (!ejercicio) {
        contenedorMensaje.textContent = 'El ejercicio seleccionado no pertenece a esta rutina.';
        return;
    }

    const numeroSerie = seriesSesion.filter(function(serie) {
        return String(serie.id_ejercicio) === String(idEjercicio);
    }).length + 1;

    seriesSesion.push({
        id_ejercicio: idEjercicio,
        nombre: traducirEjercicio(ejercicio.nombre),
        numero_serie: numeroSerie,
        repeticiones: repeticiones,
        peso: peso
    });

    selectEjercicio.value = '';
    inputRepeticiones.value = '';
    inputPeso.value = '';
    selectEjercicio.focus();

    pintarResumenSeries();
}

function pintarResumenSeries() {
    const contadorSeries = document.getElementById('contador-series');
    const resumenSeries = document.getElementById('resumen-series');
    const btnGuardarSesion = document.getElementById('btn-guardar-sesion');

    const totalSeries = seriesSesion.length;
    contadorSeries.textContent = `${totalSeries} ${totalSeries === 1 ? 'serie guardada' : 'series guardadas'}`;
    btnGuardarSesion.disabled = totalSeries === 0;

    if (totalSeries === 0) {
        resumenSeries.innerHTML = '<p class="rutina-vacia">Guarda tu primera serie para empezar a construir la sesión.</p>';
        return;
    }

    let html = '';

    seriesSesion.forEach(function(serie, index) {
        html += `
            <article class="serie-guardada">
                <div>
                    <span class="card-meta">Serie ${serie.numero_serie}</span>
                    <h3>${serie.nombre}</h3>
                    <p>${serie.repeticiones} repeticiones · ${serie.peso !== '' ? serie.peso + ' kg' : 'Sin peso'}</p>
                </div>

                <button type="button" class="btn btn-secondary btn-eliminar-serie" data-index="${index}">
                    Eliminar
                </button>
            </article>
        `;
    });

    resumenSeries.innerHTML = html;

    const botonesEliminar = document.querySelectorAll('.btn-eliminar-serie');

    for (const btn of botonesEliminar) {
        btn.addEventListener('click', function() {
            eliminarSerieTemporal(Number(btn.dataset.index));
        });
    }
}

function eliminarSerieTemporal(indexSerie) {
    seriesSesion.splice(indexSerie, 1);
    recalcularNumerosSerie();
    pintarResumenSeries();
}

function recalcularNumerosSerie() {
    const contadorPorEjercicio = {};

    for (const serie of seriesSesion) {
        contadorPorEjercicio[serie.id_ejercicio] = (contadorPorEjercicio[serie.id_ejercicio] || 0) + 1;
        serie.numero_serie = contadorPorEjercicio[serie.id_ejercicio];
    }
}

const traduccionesEjercicios = {
    'Rickshaw Carry': 'Paseo con rickshaw',
    'Single-Leg Press': 'Prensa a una pierna',
    'Landmine twist': 'Giro con barra landmine',
    'Dumbbell front raise to lateral raise': 'Elevación frontal y lateral con mancuernas',
    'Palms-down wrist curl over bench': 'Curl de muñeca prono en banco',
    'Atlas Stones': 'Levantamiento de piedras Atlas',
    'Clean from Blocks': 'Cargada desde bloques',
    'Incline Hammer Curls': 'Curl martillo inclinado',
    'Side Bridge': 'Plancha lateral',
    'Smith Machine Calf Raise': 'Elevación de gemelos en máquina Smith',
    'Bench Press': 'Press de banca',
    'Pullups': 'Dominadas',
    'Triceps Pushdown': 'Extensión de tríceps en polea',
    'Leg Curl': 'Curl femoral',
    'Hip Thrust': 'Hip thrust',
};

function traducirEjercicio(nombre) {
    return traduccionesEjercicios[nombre] || nombre;
}

async function guardarSesion(event, idRutina) {
    event.preventDefault();

    const contenedorMensaje = document.getElementById('mensaje-rutina');

    contenedorMensaje.textContent = '';

    if (seriesSesion.length === 0) {
        contenedorMensaje.textContent = 'Guarda al menos una serie antes de terminar la sesión.';
        return;
    }

    const datosSesion = {
        id_rutina: idRutina,
        ejercicios: seriesSesion.map(function(serie) {
            return {
                id_ejercicio: serie.id_ejercicio,
                numero_serie: serie.numero_serie,
                repeticiones: serie.repeticiones,
                peso: serie.peso
            };
        })
    };

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

        window.location.href = `routine-view.php?id=${idRutina}`;
    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al guardar la sesión.';
    }
}
