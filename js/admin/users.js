document.addEventListener('DOMContentLoaded', iniciarPaginaUsuarios);

let idUsuarioActual = null;

function iniciarPaginaUsuarios() {
    const formUsuario = document.getElementById('form-usuario');
    const contenedorMensaje = document.getElementById('mensaje-usuarios');
    const contenedorUsuarios = document.getElementById('lista-usuarios');
    const mensajeCrear = document.getElementById('mensaje-crear-usuario');

    cargarUsuarios(contenedorMensaje, contenedorUsuarios);

    formUsuario.addEventListener('submit', function(event) {
        event.preventDefault();
        crearUsuario(formUsuario, mensajeCrear, contenedorMensaje, contenedorUsuarios);
    });
}

async function cargarUsuarios(contenedorMensaje, contenedorUsuarios) {
    contenedorMensaje.textContent = '';
    contenedorUsuarios.innerHTML = '';

    try {
        const respuesta = await fetch('../../backend/admin/list-users.php');
        const data = await respuesta.json();

        if (!data.success) {
            contenedorMensaje.textContent = data.message;
            return;
        }

        idUsuarioActual = Number(data.current_user_id);
        pintarUsuarios(data.usuarios, contenedorUsuarios);
    } catch (error) {
        contenedorMensaje.textContent = 'Ha ocurrido un error al cargar los usuarios.';
    }
}

function pintarUsuarios(usuarios, contenedorUsuarios) {
    if (usuarios.length === 0) {
        contenedorUsuarios.innerHTML = '<p class="usuarios-vacios">No hay usuarios registrados.</p>';
        return;
    }

    let html = '';

    for (const usuario of usuarios) {
        const idUsuario = Number(usuario.id_usuario);
        const esUsuarioActual = idUsuario === idUsuarioActual;
        const nombreCompleto = `${usuario.nombre} ${usuario.apellidos || ''}`.trim();

        html += `
            <article class="usuario-card" data-id="${idUsuario}">
                <div class="usuario-info">
                    <span class="badge-rol rol-${escaparHtml(usuario.rol)}">${escaparHtml(usuario.rol)}</span>
                    <h3>${escaparHtml(nombreCompleto)}</h3>
                    <p>${escaparHtml(usuario.email)}</p>
                    <span class="usuario-alias">@${escaparHtml(usuario.nombre_usuario)}</span>
                </div>

                <div class="usuario-acciones">
                    <label>
                        Rol
                        <select class="control-formulario select-rol" data-id="${idUsuario}" data-rol-anterior="${escaparHtml(usuario.rol)}" ${esUsuarioActual ? 'disabled' : ''}>
                            <option value="usuario" ${usuario.rol === 'usuario' ? 'selected' : ''}>Usuario</option>
                            <option value="admin" ${usuario.rol === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </label>

                    <button type="button" class="btn btn-danger btn-eliminar-usuario" data-id="${idUsuario}" ${esUsuarioActual ? 'disabled' : ''}>
                        Eliminar
                    </button>
                </div>
            </article>
        `;
    }

    contenedorUsuarios.innerHTML = html;

    for (const select of document.querySelectorAll('.select-rol')) {
        select.addEventListener('change', function() {
            actualizarRol(select);
        });
    }

    for (const boton of document.querySelectorAll('.btn-eliminar-usuario')) {
        boton.addEventListener('click', function() {
            eliminarUsuario(boton.dataset.id);
        });
    }
}

async function crearUsuario(formUsuario, mensajeCrear, contenedorMensaje, contenedorUsuarios) {
    mensajeCrear.textContent = '';

    const formData = new FormData(formUsuario);
    const payload = Object.fromEntries(formData.entries());

    try {
        const respuesta = await fetch('../../backend/admin/create-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await respuesta.json();
        mensajeCrear.textContent = data.message;

        if (!data.success) {
            return;
        }

        formUsuario.reset();
        cargarUsuarios(contenedorMensaje, contenedorUsuarios);
    } catch (error) {
        mensajeCrear.textContent = 'Ha ocurrido un error al crear el usuario.';
    }
}

async function actualizarRol(select) {
    const rolAnterior = select.dataset.rolAnterior || select.value;
    const idUsuario = select.dataset.id;

    select.disabled = true;

    try {
        const respuesta = await fetch('../../backend/admin/update-role.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_usuario: idUsuario,
                rol: select.value
            })
        });

        const data = await respuesta.json();

        if (!data.success) {
            alert(data.message);
            select.value = rolAnterior;
            return;
        }

        window.location.reload();
    } catch (error) {
        alert('Ha ocurrido un error al actualizar el rol.');
        select.value = rolAnterior;
    } finally {
        select.disabled = false;
    }
}

async function eliminarUsuario(idUsuario) {
    const confirmar = window.confirm('Vas a eliminar este usuario y todos sus datos asociados. Esta acción no se puede deshacer.');

    if (!confirmar) {
        return;
    }

    try {
        const respuesta = await fetch('../../backend/admin/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_usuario: idUsuario
            })
        });

        const data = await respuesta.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        window.location.reload();
    } catch (error) {
        alert('Ha ocurrido un error al eliminar el usuario.');
    }
}

function escaparHtml(valor) {
    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
