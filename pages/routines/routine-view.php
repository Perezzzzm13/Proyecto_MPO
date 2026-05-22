<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../auth/login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de rutina - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/header.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/components/cards.css">
    <link rel="stylesheet" href="../../css/components/messages.css">
    <link rel="stylesheet" href="../../css/pages/routines.css">
</head>
<body>
    <?php $paginaActiva = 'rutinas'; require_once __DIR__ . '/../partials/app-header.php'; ?>

        <main>
            <section class="cabecera-pagina cabecera-detalle-rutina">
                <div class="contenido-cabecera">
                    <span class="etiqueta">Detalle de rutina</span>
                    <h1 id="nombre-rutina">Rutina</h1>
                    <p id="descripcion-rutina" class="entradilla-pagina"></p>
                </div>

                <a href="routines.php" class="btn btn-secondary btn-volver-detalle">Volver</a>

                <div class="acciones-rutina">
                    <a href="#" id="btn-iniciar-rutina" class="btn btn-accent">Iniciar rutina</a>
                    <a href="#" id="btn-aniadir-ejercicio" class="btn btn-secondary">Añadir ejercicio</a>
                    <a href="#" id="btn-editar-rutina" class="btn btn-secondary">Editar</a>
                    <button type="button" id="btn-eliminar-rutina" class="btn btn-danger">Eliminar</button>
                </div>
            </section>

            <section id="mensaje-rutina" class="mensaje-formulario"></section>
            <section class="panel-detalle-rutina">
                <div class="cabecera-seccion">
                    <div>
                        <span class="etiqueta">Ejercicios</span>
                        <h2>Ejercicios de la rutina</h2>
                    </div>
                    <span id="contador-ejercicios" class="indicador-metrica"></span>
                </div>

                <section id="lista-ejercicios"></section>
            </section>
        </main>
    </div>

    <script src="../../js/routines/routine-view.js"></script>
</body>
</html>
