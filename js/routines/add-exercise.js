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

  btnVolverRutina.href = `routine-view.php?id=${idRutina}`;

  const ejerciciosRutina = new Set();
  // Se guarda una copia de los ejercicios actuales para desactivar duplicados en los resultados.
  cargarEjerciciosRutina(idRutina, ejerciciosRutina, contenedorMensaje);

  formulario.addEventListener('submit', function (evento) {
    buscarEjercicios(evento, idRutina, ejerciciosRutina);
  });
}

async function cargarEjerciciosRutina(idRutina, ejerciciosRutina, contenedorMensaje) {
  try {
    const respuesta = await fetch(`../../backend/routines/get.php?id=${idRutina}`);
    const data = await respuesta.json();

    if (!data.success) {
      contenedorMensaje.textContent = data.message;
      return;
    }

    for (const ejercicio of data.ejercicios) {
      ejerciciosRutina.add(normalizarNombreEjercicio(ejercicio.nombre));
      ejerciciosRutina.add(normalizarNombreEjercicio(traducirEjercicio(ejercicio.nombre)));
    }
  } catch (error) {
    contenedorMensaje.textContent = 'No se pudieron comprobar los ejercicios de la rutina.';
  }
}

async function buscarEjercicios(evento, idRutina, ejerciciosRutina) {
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

    // encodeURIComponent evita que espacios o caracteres especiales rompan la URL.
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

    pintarEjercicios(data.ejercicios, resultadosEjercicios, idRutina, ejerciciosRutina);
  } catch (error) {
    contenedorMensaje.textContent = 'Ha ocurrido un error al buscar ejercicios.';
  }
}

function pintarEjercicios(ejercicios, contenedor, idRutina, ejerciciosRutina) {
  let html = '';

  for (const ejercicio of ejercicios) {
    const musculoTraducido = traduccionesMusculos[ejercicio.muscle] || ejercicio.muscle;
    const dificultadTraducida = traduccionesDificultad[ejercicio.difficulty] || ejercicio.difficulty;
    const tipoTraducido = traduccionesTipo[ejercicio.type] || ejercicio.type;
    const nombreTraducido = traducirEjercicio(ejercicio.name);
    // Se compara el nombre original y el traducido para detectar duplicados.
    const ejercicioYaAnadido = ejerciciosRutina.has(normalizarNombreEjercicio(ejercicio.name)) ||
      ejerciciosRutina.has(normalizarNombreEjercicio(nombreTraducido));

    html += `
      <article class="card-ejercicio">
        <div>
          <span class="card-meta">${dificultadTraducida}</span>
          <h2>${nombreTraducido}</h2>

          <div class="lista-detalle">
            <p><strong>Musculo:</strong> ${musculoTraducido}</p>
            <p><strong>Tipo:</strong> ${tipoTraducido}</p>
          </div>
        </div>

        <button
          type="button"
          class="btn btn-accent btn-anadir-ejercicio"
          data-id-rutina="${idRutina}"
          data-nombre="${nombreTraducido}"
          data-musculo="${ejercicio.muscle}"
          data-tipo="${ejercicio.type}"
          data-dificultad="${ejercicio.difficulty}"
          ${ejercicioYaAnadido ? 'disabled' : ''}
        >
          ${ejercicioYaAnadido ? 'Ya pertenece a la rutina' : 'Añadir a rutina'}
        </button>
      </article>
    `;
  }

  contenedor.innerHTML = html;

  const botonesAnadir = document.querySelectorAll('.btn-anadir-ejercicio');

  for (const btn of botonesAnadir) {
    btn.addEventListener('click', anadirEjercicioARutina);
  }
}

async function anadirEjercicioARutina(evento) {
  const btn = evento.target;
  const contenedorMensaje = document.getElementById('mensaje-ejercicio');

  const datosEjercicio = {
    id_rutina: btn.dataset.idRutina,
    nombre: btn.dataset.nombre,
    musculo: btn.dataset.musculo,
    tipo: btn.dataset.tipo,
    dificultad: btn.dataset.dificultad
  };

  contenedorMensaje.textContent = '';

  try {
    const respuesta = await fetch('../../backend/exercises/add-to-routine.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(datosEjercicio)
    });

    const data = await respuesta.json();

    if (!data.success) {
      contenedorMensaje.textContent = data.message;
      return;
    }

    window.location.href = `routine-view.php?id=${datosEjercicio.id_rutina}`;
  } catch (error) {
    contenedorMensaje.textContent = 'Ha ocurrido un error al añadir el ejercicio.';
  }
}

const traduccionesMusculos = {
  abdominals: 'Abdominales',
  abductors: 'Abductores',
  adductors: 'Aductores',
  biceps: 'Biceps',
  calves: 'Gemelos',
  chest: 'Pecho',
  forearms: 'Antebrazos',
  glutes: 'Gluteos',
  hamstrings: 'Femoral',
  lats: 'Espalda',
  lower_back: 'Lumbar',
  middle_back: 'Espalda media',
  neck: 'Cuello',
  quadriceps: 'Cuadriceps',
  traps: 'Trapecio',
  triceps: 'Triceps',
};

const traduccionesDificultad = {
  beginner: 'Principiante',
  intermediate: 'Intermedio',
  expert: 'Avanzado',
};

const traduccionesTipo = {
  cardio: 'Cardio',
  olympic_weightlifting: 'Halterofilia',
  plyometrics: 'Pliometria',
  powerlifting: 'Powerlifting',
  strength: 'Fuerza',
  stretching: 'Estiramientos',
  strongman: 'Strongman',
};

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
  'Incline Bench Press': 'Press inclinado',
  'Dumbbell Bench Press': 'Press de banca con mancuernas',
  'Pushups': 'Flexiones',
  'Pullups': 'Dominadas',
  'Chin-Up': 'Dominada supina',
  'Squat': 'Sentadilla',
  'Deadlift': 'Peso muerto',
  'Leg Press': 'Prensa de piernas',
  'Shoulder Press': 'Press de hombro',
  'Lateral Raise': 'Elevacion lateral',
  'Biceps Curl': 'Curl de biceps',
  'Triceps Pushdown': 'Extension de triceps en polea',
  'Leg Curl': 'Curl femoral',
  'Hip Thrust': 'Hip thrust',
};

function traducirEjercicio(nombre) {
  return traduccionesEjercicios[nombre] || nombre;
}

function normalizarNombreEjercicio(nombre) {
  // Quita mayusculas y tildes para comparar nombres de forma mas fiable.
  return nombre
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();
}
