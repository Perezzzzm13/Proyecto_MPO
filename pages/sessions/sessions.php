<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../auth/login.html');
    exit;
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mis sesiones - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css" />
    <link rel="stylesheet" href="../../css/base/variables.css" />
    <link rel="stylesheet" href="../../css/base/global.css" />
    <link rel="stylesheet" href="../../css/components/header.css" />
    <link rel="stylesheet" href="../../css/components/buttons.css" />
    <link rel="stylesheet" href="../../css/components/cards.css" />
    <link rel="stylesheet" href="../../css/components/messages.css" />
    <link rel="stylesheet" href="../../css/components/modal.css" />
    <link rel="stylesheet" href="../../css/pages/sessions.css" />
  </head>
  <body>
    <?php $paginaActiva = 'sesiones'; require_once __DIR__ . '/../partials/app-header.php'; ?>

      <main>
        <section class="cabecera-sesiones">
          <div>
            <span class="etiqueta">Historial</span>
            <h1>Mis sesiones</h1>
            <p class="entradilla-pagina">Consulta tus entrenamientos guardados y revisa la evolución de tus marcas.</p>
          </div>

          <section class="acciones-sesiones">
            <a href="../dashboard/dashboard.php" class="btn btn-secondary">Volver al dashboard</a>
          </section>
        </section>

        <section id="mensaje-sesiones" class="mensaje-formulario"></section>
        <section id="lista-sesiones"></section>
      </main>
    </div>

    <div id="modal-sesion" class="modal oculto">
      <div class="modal-contenido">
        <button type="button" id="cerrar-modal-sesion" class="modal-cerrar">×</button>

        <section class="cabecera-sesion">
          <h2 id="modal-nombre-rutina">Detalle de sesión</h2>
          <p id="modal-fecha-sesion"></p>
        </section>

        <section id="modal-detalle-ejercicios"></section>
      </div>
    </div>

    <div id="modal-estadisticas" class="modal oculto">
      <div class="modal-contenido">
        <button type="button" id="cerrar-modal-estadisticas" class="modal-cerrar">×</button>

        <section class="cabecera-sesion">
          <h2>Estadísticas de sesión</h2>
          <p id="modal-fecha-estadisticas"></p>
          <p id="modal-fecha-comparada"></p>
        </section>

        <section id="modal-contenido-estadisticas"></section>
      </div>
    </div>

    <script src="../../js/sessions/sessions-list.js?v=<?php echo filemtime(__DIR__ . '/../../js/sessions/sessions-list.js'); ?>"></script>
  </body>
</html>
