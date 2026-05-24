document.addEventListener('DOMContentLoaded', iniciarPaginaRutinas);

function iniciarPaginaRutinas() {
    const contenedorMensaje = document.getElementById('mensaje-rutinas');
    const contenedorRutinas = document.getElementById('lista-rutinas');

    cargarRutinas(contenedorMensaje, contenedorRutinas);
}

async function cargarRutinas(contenedorMensaje, contenedorRutinas) {
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
            contenedorMensaje.textContent = 'Todavia no tienes rutinas creadas';
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
            <article class="card card-rutina">
                <div>
                    <span class="card-meta">Rutina activa</span>
                    <h2>${rutina.nombre}</h2>
                    <p>${rutina.descripcion ? rutina.descripcion : 'Sin descripcion'}</p>
                </div>
                <div class="acciones-card-rutina">
                    <a href="routine-view.php?id=${rutina.id_rutina}" class="btn btn-primary">Ver rutina</a>
                    <div class="acciones-card-rutina-secundarias">
                        <a href="edit-routine.php?id=${rutina.id_rutina}" class="btn btn-secondary">Editar</a>
                        <button type="button" class="btn btn-danger btn-eliminar-rutina" data-id="${rutina.id_rutina}">
                            Eliminar
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    contenedorRutinas.innerHTML = html;

    const botonesEliminar = document.querySelectorAll('.btn-eliminar-rutina');

    for (const btn of botonesEliminar) {
        btn.addEventListener('click', function() {
            eliminarRutina(btn.dataset.id, contenedorRutinas);
        });
    }
}

async function eliminarRutina(idRutina) {
    const confirmar = window.confirm('Vas a eliminar esta rutina y sus datos asociados. Esta acción no se puede deshacer.');

    if (!confirmar) {
        return;
    }

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
            alert(data.message);
            return;
        }

        window.location.reload();
    } catch (error) {
        alert('Ha ocurrido un error al eliminar la rutina.');
    }
}
