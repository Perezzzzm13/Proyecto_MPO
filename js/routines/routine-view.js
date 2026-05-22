document.addEventListener('DOMContentLoaded', iniciarVistaRutina);

function iniciarVistaRutina() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');
    const contenedorMensaje = document.getElementById('mensaje-rutina');
    const btnAniadirEjercicio = document.getElementById('btn-aniadir-ejercicio');
    const btnIniciarRutina = document.getElementById('btn-iniciar-rutina');
    const btnEditarRutina = document.getElementById('btn-editar-rutina');
    const btnEliminarRutina = document.getElementById('btn-eliminar-rutina');

    if (!idRutina) {
        contenedorMensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    btnAniadirEjercicio.href = `add-exercise.php?id=${idRutina}`;
    btnIniciarRutina.href = `start-routine.php?id=${idRutina}`;
    btnEditarRutina.href = `edit-routine.php?id=${idRutina}`;
    btnEliminarRutina.addEventListener('click', function() {
        eliminarRutina(idRutina, contenedorMensaje);
    });

    cargarRutina(idRutina, contenedorMensaje);
}

async function cargarRutina(idRutina, contenedorMensaje) {
    const nombreRutina = document.getElementById('nombre-rutina');
    const descripcionRutina = document.getElementById('descripcion-rutina');
    const listaEjercicios = document.getElementById('lista-ejercicios');
    const contadorEjercicios = document.getElementById('contador-ejercicios');

    contenedorMensaje.textContent = '';
    nombreRutina.textContent = '';
    descripcionRutina.textContent = '';
    listaEjercicios.innerHTML = '';
    contadorEjercicios.textContent = '';

    try {
        const respuesta = await fetch(`../../backend/routines/get.php?id=${idRutina}`);
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        nombreRutina.textContent = data.rutina.nombre;
        descripcionRutina.textContent = data.rutina.descripcion ? data.rutina.descripcion : 'Sin descripcion';
        contadorEjercicios.textContent = `${data.ejercicios.length} ${data.ejercicios.length === 1 ? 'ejercicio' : 'ejercicios'}`;

        if (data.ejercicios.length === 0) {
            listaEjercicios.innerHTML = '<p class="rutina-vacia">Esta rutina todavia no tiene ejercicios. Añade uno para empezar a entrenar con ella.</p>';
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
            <article class="card card-ejercicio">
                <div class="fila-titulo-ejercicio">
                    <span class="numero-ejercicio">${ejercicio.orden}</span>
                    <h3>${traducirEjercicio(ejercicio.nombre)}</h3>
                </div>
            </article>
        `;
    }

    listaEjercicios.innerHTML = html;
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

async function eliminarRutina(idRutina, contenedorMensaje) {
    const confirmar = window.confirm('Vas a eliminar esta rutina y sus datos asociados. Esta accion no se puede deshacer.');

    if (!confirmar) {
        return;
    }

    contenedorMensaje.textContent = '';

    try {
        const respuesta = await fetch('../../backend/routines/delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_rutina: idRutina
            })
        });

            const data = await respuesta.json();

            if (!data.success) {
                contenedorMensaje.textContent = data.message;
                return;
            }

        window.location.href = 'routines.php';
    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al eliminar la rutina.';
    }
}
