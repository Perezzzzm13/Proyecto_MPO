document.addEventListener('DOMContentLoaded', iniciarPaginaAnadirEjercicio);

function iniciarPaginaAnadirEjercicio() {
    const parametrosUrl = new URLSearchParams(window.location.search);
    const idRutina = parametrosUrl.get('id');

    const formulario = document.getElementById('form-buscar-ejercicios');
    const contenedorMensaje = document.getElementById('mensaje-ejercicio');
    const btnVolverRutina = document.getElementById('btn-volver-rutina');

    if (!idRutina) {
        contenedorMensaje.textContent = 'No se ha indicado ninguna rutina.';
        return;
    }

    btnVolverRutina.href = `routine-view.html?id=${idRutina}`;

    formulario.addEventListener('submit', function(evento) {
        buscarEjercicios(evento, idRutina);
    });
}

async function buscarEjercicios(evento, idRutina) {
    evento.preventDefault();

    const inputNombre = document.getElementById('nombre-ejercicio');
    const selectMusculo = document.getElementById('musculo');
    const contenedorMensaje = document.getElementById('mensaje-ejercicio');
    const resultadosEjercicios = document.getElementById('resultados-ejercicios');

    const nombre = inputNombre.value.trim();
    const musculo = selectMusculo.value;

    contenedorMensaje.textContent = '';
    resultadosEjercicios.innerHTML = '';

    try {
        let url = '../../backend/exercises/search.php?';

        if (nombre) {
            url += `name=${encodeURIComponent(nombre)}&`;
        }

        if (musculo) {
            url += `muscle=${encodeURIComponent(musculo)}`;
        }

        const respuesta = await fetch(url);
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        if (data.ejercicios.length === 0) {
            contenedorMensaje.textContent = 'No se encontraron ejercicios.';
            return;
        }

        pintarEjercicios(data.ejercicios, resultadosEjercicios, idRutina);

    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al buscar ejercicios.';
    }
}

function pintarEjercicios(ejercicios, contenedor, idRutina) {
    let html = '';

    for (const ejercicio of ejercicios) {
        const musculoTraducido = traduccionesMusculos[ejercicio.muscle] || ejercicio.muscle;
        const dificultadTraducida = traduccionesDificultad[ejercicio.difficulty] || ejercicio.difficulty;
        const tipoTraducido = traduccionesTipo[ejercicio.type] || ejercicio.type;

        html += `
            <article class="card-ejercicio">
                <h2>${ejercicio.name}</h2>

                <p><strong>Músculo:</strong> ${musculoTraducido}</p>
                <p><strong>Tipo:</strong> ${tipoTraducido}</p>
                <p><strong>Dificultad:</strong> ${dificultadTraducida}</p>

                <button 
                    type="button" 
                    class="btn btn-primary btn-anadir-ejercicio"
                    data-id-rutina="${idRutina}"
                    data-nombre="${ejercicio.name}"
                    data-musculo="${ejercicio.muscle}"
                    data-tipo="${ejercicio.type}"
                    data-dificultad="${ejercicio.difficulty}"
                >
                    Añadir a rutina
                </button>
            </article>
        `;
    }

    contenedor.innerHTML = html;
}

const traduccionesMusculos = {
    abdominals: 'Abdominales',
    abductors: 'Abductores',
    adductors: 'Aductores',
    biceps: 'Bíceps',
    calves: 'Gemelos',
    chest: 'Pecho',
    forearms: 'Antebrazos',
    glutes: 'Glúteos',
    hamstrings: 'Femoral',
    lats: 'Espalda',
    lower_back: 'Lumbar',
    middle_back: 'Espalda media',
    neck: 'Cuello',
    quadriceps: 'Cuádriceps',
    traps: 'Trapecio',
    triceps: 'Tríceps'
};

const traduccionesDificultad = {
    beginner: 'Principiante',
    intermediate: 'Intermedio',
    expert: 'Avanzado'
};

const traduccionesTipo = {
    cardio: 'Cardio',
    olympic_weightlifting: 'Halterofilia',
    plyometrics: 'Pliometría',
    powerlifting: 'Powerlifting',
    strength: 'Fuerza',
    stretching: 'Estiramientos',
    strongman: 'Strongman'
};