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
    <title>Añadir ejercicio - GymTracker</title>

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
            <section class="cabecera-pagina">
                <div class="contenido-cabecera">
                    <span class="etiqueta">Biblioteca</span>
                    <h1>Añadir ejercicio</h1>
                    <p class="entradilla-pagina">Busca ejercicios por nombre o músculo y añádelos a tu rutina.</p>
                </div>
            </section>

            <section class="card card-busqueda">
                <form id="form-buscar-ejercicios">
                    <div class="cuadricula-formulario">
                        <div class="campo-formulario">
                            <label for="nombre-ejercicio">Buscar por nombre</label>
                            <input type="text" id="nombre-ejercicio" name="nombre-ejercicio" placeholder="Ejemplo: bench press">
                        </div>

                        <div class="campo-formulario">
                            <label for="musculo">Filtrar por músculo</label>
                            <select id="musculo" name="musculo">
                                <option value="">Todos</option>
                                <option value="chest">Pecho</option>
                                <option value="lats">Espalda</option>
                                <option value="biceps">Bíceps</option>
                                <option value="triceps">Tríceps</option>
                                <option value="shoulders">Hombros</option>
                                <option value="quadriceps">Cuádriceps</option>
                                <option value="hamstrings">Femoral</option>
                                <option value="glutes">Glúteos</option>
                                <option value="abdominals">Abdominales</option>
                            </select>
                        </div>
                    </div>

                    <div id="mensaje-ejercicio" class="mensaje-formulario span-2"></div>

                    <div class="acciones-formulario span-2">
                        <button type="submit" class="btn btn-accent">Buscar ejercicios</button>
                        <a href="#" id="btn-volver-rutina" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </section>

            <section id="resultados-ejercicios"></section>
        </main>
    </div>

    <script src="../../js/routines/add-exercise.js"></script>
</body>
</html>
