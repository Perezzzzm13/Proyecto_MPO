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
    <title>Iniciar rutina - GymTracker</title>

    <link rel="stylesheet" href="../../css/base/reset.css">
    <link rel="stylesheet" href="../../css/base/variables.css">
    <link rel="stylesheet" href="../../css/base/global.css">
    <link rel="stylesheet" href="../../css/components/header.css">
    <link rel="stylesheet" href="../../css/components/buttons.css">
    <link rel="stylesheet" href="../../css/components/forms.css">
    <link rel="stylesheet" href="../../css/components/cards.css">
    <link rel="stylesheet" href="../../css/components/messages.css">
    <link rel="stylesheet" href="../../css/pages/routines.css">
</head>
<body>
    <?php $paginaActiva = 'rutinas'; require_once __DIR__ . '/../partials/app-header.php'; ?>

        <main>
            <section class="cabecera-rutina">
                <span class="etiqueta">Sesión activa</span>
                <h1 id="nombre-rutina">Rutina</h1>
                <p id="descripcion-rutina" class="entradilla-pagina"></p>
            </section>

            <section id="mensaje-rutina" class="mensaje-formulario"></section>

            <form id="form-sesion-rutina" class="panel-formulario-sesion">
                <div class="cabecera-seccion">
                    <div>
                        <span class="etiqueta">Registro</span>
                        <h2>Series de entrenamiento</h2>
                    </div>
                    <span id="contador-series" class="indicador-metrica">0 series guardadas</span>
                </div>

                <section id="registro-series" class="panel-registro-serie">
                    <div class="cuadricula-registro-serie">
                        <div class="campo-formulario">
                            <label for="id-ejercicio">Ejercicio</label>
                            <select id="id-ejercicio" name="id-ejercicio">
                                <option value="">Selecciona ejercicio</option>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="repeticiones-serie">Repeticiones</label>
                            <input type="number" id="repeticiones-serie" name="repeticiones-serie" min="0">
                        </div>

                        <div class="campo-formulario">
                            <label for="peso-serie">Peso (kg)</label>
                            <input type="number" id="peso-serie" name="peso-serie" min="0" step="0.5">
                        </div>

                        <button type="button" id="btn-guardar-serie" class="btn btn-secondary">Guardar serie</button>
                    </div>
                </section>

                <section class="panel-series-guardadas">
                    <div class="cabecera-seccion cabecera-compacta">
                        <div>
                            <span class="etiqueta">Sesión actual</span>
                            <h2>Series guardadas</h2>
                        </div>
                    </div>

                    <section id="resumen-series"></section>
                </section>

                <section class="acciones-formulario">
                    <button type="submit" id="btn-guardar-sesion" class="btn btn-accent" disabled>Guardar sesión</button>
                    <a href="#" id="btn-volver-rutina" class="btn btn-secondary">Volver</a>
                </section>
            </form>
        </main>
    </div>

    <script src="../../js/routines/start-routine.js"></script>
</body>
</html>
