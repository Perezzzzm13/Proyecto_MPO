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
    <title>Crear rutina - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/header.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/components/cards.css">
    <link rel="stylesheet" href="../../css/components/forms.css">
    <link rel="stylesheet" href="../../css/components/messages.css">
    <link rel="stylesheet" href="../../css/pages/routines.css">
</head>
<body>
    <?php $paginaActiva = 'rutinas'; require_once __DIR__ . '/../partials/app-header.php'; ?>

        <main>
            <section class="cabecera-pagina">
                <div class="contenido-cabecera">
                    <span class="etiqueta">Nueva rutina</span>
                    <h1>Crear rutina</h1>
                    <p class="entradilla-pagina">Define un nombre y una descripción para empezar a construir tu entrenamiento.</p>
                </div>
            </section>

            <section class="card card-formulario">
                <form id="form-crear-rutina">
                    <div class="grupo-formulario">
                        <label for="nombre" class="etiqueta-formulario">Nombre de la rutina</label>
                        <input type="text" id="nombre" name="nombre" maxlength="50" required class="control-formulario">
                    </div>

                    <div class="grupo-formulario">
                        <label for="descripcion" class="etiqueta-formulario">Descripción</label>
                        <textarea id="descripcion" name="descripcion" maxlength="255" rows="4" class="control-formulario"></textarea>
                    </div>

                    <div id="mensaje-rutina" class="mensaje-formulario"></div>

                    <div class="acciones-formulario">
                        <button type="submit" class="btn btn-accent">Guardar rutina</button>
                        <a href="routines.php" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script src="../../js/routines/routines-create.js"></script>
</body>
</html>
