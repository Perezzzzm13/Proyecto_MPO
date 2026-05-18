<?php
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_sesion = $_GET['id'] ?? '';

if ($id_sesion === '' || !is_numeric($id_sesion)) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

try {

    $sqlSesionActual = "SELECT 
                            s.id_sesion,
                            s.id_rutina,
                            s.fecha_hora,
                            r.nombre AS nombre_rutina
                        FROM sesiones s
                        INNER JOIN rutinas r
                            ON s.id_rutina = r.id_rutina
                        WHERE s.id_sesion = :id_sesion
                          AND s.id_usuario = :id_usuario";

    $stmtSesionActual = $conexion->prepare($sqlSesionActual);
    $stmtSesionActual->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
    $stmtSesionActual->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtSesionActual->execute();

    $sesionActual = $stmtSesionActual->fetch(PDO::FETCH_ASSOC);

    if (!$sesionActual) {
        echo json_encode([
            'success' => false,
            'message' => 'La sesión no existe o no pertenece al usuario'
        ]);
        exit;
    }

    $id_rutina = $sesionActual['id_rutina'];
    $fecha_actual = $sesionActual['fecha_hora'];

    $sqlSesionAnterior = "SELECT 
                            id_sesion,
                            fecha_hora
                          FROM sesiones
                          WHERE id_usuario = :id_usuario
                            AND id_rutina = :id_rutina
                            AND fecha_hora < :fecha_actual
                          ORDER BY fecha_hora DESC
                          LIMIT 1";

    $stmtSesionAnterior = $conexion->prepare($sqlSesionAnterior);
    $stmtSesionAnterior->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtSesionAnterior->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtSesionAnterior->bindParam(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
    $stmtSesionAnterior->execute();

    $sesionAnterior = $stmtSesionAnterior->fetch(PDO::FETCH_ASSOC);

    if (!$sesionAnterior) {
        echo json_encode([
            'success' => false,
            'message' => 'No hay una sesión anterior de esta misma rutina para comparar'
        ]);
        exit;
    }

    $id_sesion_anterior = $sesionAnterior['id_sesion'];

    $sqlEjerciciosActuales = "SELECT 
                                se.id_ejercicio,
                                e.nombre,
                                se.numero_serie,
                                se.repeticiones,
                                se.peso
                              FROM sesion_ejercicios se
                              INNER JOIN ejercicios e
                                ON se.id_ejercicio = e.id_ejercicio
                              WHERE se.id_sesion = :id_sesion";

    $stmtEjerciciosActuales = $conexion->prepare($sqlEjerciciosActuales);
    $stmtEjerciciosActuales->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
    $stmtEjerciciosActuales->execute();

    $ejerciciosActuales = $stmtEjerciciosActuales->fetchAll(PDO::FETCH_ASSOC);

    $sqlEjerciciosAnteriores = "SELECT 
                                    se.id_ejercicio,
                                    e.nombre,
                                    se.numero_serie,
                                    se.repeticiones,
                                    se.peso
                                FROM sesion_ejercicios se
                                INNER JOIN ejercicios e
                                    ON se.id_ejercicio = e.id_ejercicio
                                WHERE se.id_sesion = :id_sesion";

    $stmtEjerciciosAnteriores = $conexion->prepare($sqlEjerciciosAnteriores);
    $stmtEjerciciosAnteriores->bindParam(':id_sesion', $id_sesion_anterior, PDO::PARAM_INT);
    $stmtEjerciciosAnteriores->execute();

    $ejerciciosAnteriores = $stmtEjerciciosAnteriores->fetchAll(PDO::FETCH_ASSOC);

    $anterioresPorEjercicio = [];

    foreach ($ejerciciosAnteriores as $ejercicioAnterior) {
        $anterioresPorEjercicio[$ejercicioAnterior['id_ejercicio']] = $ejercicioAnterior;
    }

    $estadisticas = [];

    foreach ($ejerciciosActuales as $ejercicioActual) {
        $id_ejercicio = $ejercicioActual['id_ejercicio'];

        if (!isset($anterioresPorEjercicio[$id_ejercicio])) {
            $estadisticas[] = [
                'nombre' => $ejercicioActual['nombre'],
                'peso_anterior' => null,
                'peso_actual' => $ejercicioActual['peso'],
                'progreso_peso' => null,
                'reps_anteriores' => null,
                'reps_actuales' => $ejercicioActual['repeticiones'],
                'progreso_reps' => null,
                'mensaje_reps' => 'Sin datos anteriores'
            ];

            continue;
        }

        $ejercicioAnterior = $anterioresPorEjercicio[$id_ejercicio];

        $pesoAnterior = (float) $ejercicioAnterior['peso'];
        $pesoActual = (float) $ejercicioActual['peso'];

        $repsAnteriores = (int) $ejercicioAnterior['repeticiones'];
        $repsActuales = (int) $ejercicioActual['repeticiones'];

        $progresoPeso = $pesoActual - $pesoAnterior;

        $progresoReps = null;
        $mensajeReps = null;

        if ($pesoActual > $pesoAnterior) {
            $mensajeReps = 'Nuevo peso';
        } else {
            $progresoReps = $repsActuales - $repsAnteriores;
        }

        $estadisticas[] = [
            'nombre' => $ejercicioActual['nombre'],
            'peso_anterior' => $pesoAnterior,
            'peso_actual' => $pesoActual,
            'progreso_peso' => $progresoPeso,
            'reps_anteriores' => $repsAnteriores,
            'reps_actuales' => $repsActuales,
            'progreso_reps' => $progresoReps,
            'mensaje_reps' => $mensajeReps
        ];
    }

    echo json_encode([
        'success' => true,
        'sesion_actual' => $sesionActual['fecha_hora'],
        'sesion_anterior' => $sesionAnterior['fecha_hora'],
        'estadisticas' => $estadisticas
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener las estadísticas'
    ]);
}