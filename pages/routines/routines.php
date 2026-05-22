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
    <title>Mis rutinas - GymTracker</title>

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
            <section class="cabecera-pagina">
                <div class="contenido-cabecera">
                    <span class="etiqueta">Rutinas</span>
                    <h1>Mis rutinas</h1>
                    <p class="entradilla-pagina">Gestiona tus planes de entrenamiento y entra directamente a la rutina que quieras trabajar.</p>
                </div>

                <div class="acciones-rutinas">
                    <a href="create-routine.php" class="btn btn-accent">+ Crear rutina</a>
                    <a href="../dashboard/dashboard.php" class="btn btn-secondary">Volver</a>
                </div>
            </section>

            <section id="mensaje-rutinas" class="mensaje-formulario"></section>
            <section id="lista-rutinas"></section>
        </main>
    </div>

    <script src="../../js/routines/routines-list.js"></script>
</body>
</html>
