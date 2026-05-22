<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../auth/login.html');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: ../dashboard/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/header.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/components/cards.css">
    <link rel="stylesheet" href="../../css/components/forms.css">
    <link rel="stylesheet" href="../../css/components/messages.css">
    <link rel="stylesheet" href="../../css/pages/users.css">
</head>
<body>
    <?php $paginaActiva = 'usuarios'; require_once __DIR__ . '/../partials/app-header.php'; ?>

        <main>
            <section class="cabecera-pagina cabecera-usuarios">
                <div class="contenido-cabecera">
                    <span class="etiqueta">Administracion</span>
                    <h1>Usuarios</h1>
                    <p class="entradilla-pagina">Gestiona altas, bajas y permisos de las cuentas de GymTracker.</p>
                </div>
            </section>

            <section class="panel-usuarios">
                <article class="card card-formulario-usuario">
                    <div class="cabecera-card-usuarios">
                        <span class="indicador-metrica">Nuevo usuario</span>
                        <h2>Crear cuenta</h2>
                    </div>

                    <form id="form-usuario" class="form-usuario">
                        <div class="cuadricula-formulario">
                            <div class="grupo-formulario">
                                <label for="nombre_usuario" class="etiqueta-formulario">Usuario</label>
                                <input type="text" id="nombre_usuario" name="nombre_usuario" class="control-formulario" maxlength="20" required>
                            </div>

                            <div class="grupo-formulario">
                                <label for="rol" class="etiqueta-formulario">Rol</label>
                                <select id="rol" name="rol" class="control-formulario" required>
                                    <option value="usuario">Usuario</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="grupo-formulario">
                                <label for="nombre" class="etiqueta-formulario">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="control-formulario" maxlength="30" required>
                            </div>

                            <div class="grupo-formulario">
                                <label for="apellidos" class="etiqueta-formulario">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" class="control-formulario" maxlength="40">
                            </div>

                            <div class="grupo-formulario">
                                <label for="email" class="etiqueta-formulario">Correo electronico</label>
                                <input type="email" id="email" name="email" class="control-formulario" maxlength="100" required>
                            </div>

                            <div class="grupo-formulario">
                                <label for="password" class="etiqueta-formulario">Contraseña</label>
                                <input type="password" id="password" name="password" class="control-formulario" minlength="6" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Añadir usuario</button>
                        <p id="mensaje-crear-usuario" class="mensaje-formulario"></p>
                    </form>
                </article>

                <article class="card card-listado-usuarios">
                    <div class="cabecera-card-usuarios">
                        <span class="indicador-metrica">Cuentas</span>
                        <h2>Listado de usuarios</h2>
                    </div>

                    <section id="mensaje-usuarios" class="mensaje-formulario"></section>
                    <section id="lista-usuarios" class="lista-usuarios"></section>
                </article>
            </section>
        </main>
    </div>

    <script src="../../js/admin/users.js"></script>
</body>
</html>
