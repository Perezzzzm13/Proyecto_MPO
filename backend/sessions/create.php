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

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$id_usuario = $_SESSION['id_usuario'];
$id_rutina = $data['id_rutina'] ?? '';
$ejercicios = $data['ejercicios'] ?? [];

if ($id_rutina === '' || !is_numeric($id_rutina)) {
    echo json_encode([
        'success' => false,
        'message' => 'Rutina no válida'
    ]);
    exit;
}

if (empty($ejercicios)) {
    echo json_encode([
        'success' => false,
        'message' => 'No hay series para guardar'
    ]);
    exit;
}

try {
    // La sesion y sus series se guardan juntas; si algo falla, no queda media sesion grabada.
    $conexion->beginTransaction();

    $sqlRutina = "SELECT id_rutina
                  FROM rutinas
                  WHERE id_rutina = :id_rutina
                    AND id_usuario = :id_usuario";

    $stmtRutina = $conexion->prepare($sqlRutina);
    $stmtRutina->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtRutina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtRutina->execute();

    $rutina = $stmtRutina->fetch(PDO::FETCH_ASSOC);

    // Comprobacion de propiedad de la rutina antes de registrar el entrenamiento.
    if (!$rutina) {
        $conexion->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'La rutina no existe o no pertenece al usuario'
        ]);
        exit;
    }

    $fecha_hora = (new DateTime('now', new DateTimeZone('Europe/Madrid')))->format('Y-m-d H:i:s');

    $sqlSesion = "INSERT INTO sesiones (id_usuario, id_rutina, fecha_hora)
                  VALUES (:id_usuario, :id_rutina, :fecha_hora)";

    $stmtSesion = $conexion->prepare($sqlSesion);
    $stmtSesion->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtSesion->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtSesion->bindParam(':fecha_hora', $fecha_hora, PDO::PARAM_STR);
    $stmtSesion->execute();

    $id_sesion = $conexion->lastInsertId();

    $sqlSesionEjercicio = "INSERT INTO sesion_ejercicios
                            (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
                           VALUES
                            (:id_sesion, :id_ejercicio, :numero_serie, :repeticiones, :peso)";

    $stmtSesionEjercicio = $conexion->prepare($sqlSesionEjercicio);
    $seriesGuardadas = 0;

    foreach ($ejercicios as $ejercicio) {
        $id_ejercicio = $ejercicio['id_ejercicio'] ?? '';
        $numero_serie = $ejercicio['numero_serie'] ?? '';
        $repeticiones = $ejercicio['repeticiones'] ?? null;
        $peso = $ejercicio['peso'] ?? null;

        // Si una serie llega incompleta, se ignora y se sigue con el resto.
        if ($id_ejercicio === '' || !is_numeric($id_ejercicio)) {
            continue;
        }

        if ($numero_serie === '' || !is_numeric($numero_serie)) {
            continue;
        }

        if ($repeticiones === '') {
            $repeticiones = null;
        }

        if ($peso === '') {
            $peso = null;
        }

        $stmtSesionEjercicio->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmtSesionEjercicio->bindParam(':id_ejercicio', $id_ejercicio, PDO::PARAM_INT);
        $stmtSesionEjercicio->bindParam(':numero_serie', $numero_serie, PDO::PARAM_INT);
        $stmtSesionEjercicio->bindParam(':repeticiones', $repeticiones, PDO::PARAM_INT);
        $stmtSesionEjercicio->bindParam(':peso', $peso);
        $stmtSesionEjercicio->execute();
        $seriesGuardadas++;
    }

    if ($seriesGuardadas === 0) {
        // Sin series validas no tiene sentido conservar la cabecera de la sesion.
        $conexion->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'No hay series válidas para guardar'
        ]);
        exit;
    }

    $conexion->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Sesión guardada correctamente'
    ]);
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la sesión'
    ]);
}
