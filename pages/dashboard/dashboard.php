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
    <title>Dashboard - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/header.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/components/cards.css">
    <link rel="stylesheet" href="../../css/pages/dashboard.css">
</head>
<body>
    <?php $paginaActiva = 'panel'; require_once __DIR__ . '/../partials/app-header.php'; ?>

        <main>
            <section class="cabecera-panel">
                <span class="etiqueta">Panel personal</span>
                <h1>Bienvenido a GymTracker</h1>
                <p class="entradilla-pagina">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>. Organiza tus rutinas, registra tus sesiones y revisa tu progreso sin complicarte.</p>
            </section>

            <section class="acciones-panel">
                <article class="card-panel card-panel-destacada">
                    <div>
                        <span class="icono-panel">R</span>
                        <span class="indicador-metrica">Rutinas</span>
                        <h2>Planifica tus entrenamientos</h2>
                        <p>Crea rutinas claras y accede rápido a cada ejercicio.</p>
                    </div>
                    <a href="../routines/routines.php" class="btn btn-primary">Ver rutinas</a>
                </article>

                <article class="card-panel">
                    <div>
                        <span class="icono-panel">S</span>
                        <span class="indicador-metrica">Sesiones</span>
                        <h2>Revisa lo que has hecho</h2>
                        <p>Consulta entrenamientos guardados y compara tus datos.</p>
                    </div>
                    <a href="../sessions/sessions.php" class="btn btn-primary">Ver sesiones</a>
                </article>

                <article class="card-panel">
                    <div>
                        <span class="icono-panel">C</span>
                        <span class="indicador-metrica">Cuenta</span>
                        <h2>Termina con seguridad</h2>
                        <p>Cierra la sesión cuando acabes de trabajar.</p>
                    </div>
                    <a href="../../backend/auth/logout.php" class="btn btn-secondary">Cerrar sesión</a>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
