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
$nombre = trim($data['nombre'] ?? '');
$musculo = trim($data['musculo'] ?? '');
$tipo = trim($data['tipo'] ?? '');
$dificultad = trim($data['dificultad'] ?? '');

if ($id_rutina === '' || !is_numeric($id_rutina)) {
    echo json_encode([
        'success' => false,
        'message' => 'Rutina no válida'
    ]);
    exit;
}

if ($nombre === '') {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre del ejercicio es obligatorio'
    ]);
    exit;
}

$descripcion = 'Tipo: ' . $tipo . ' | Dificultad: ' . $dificultad;

try {
    $sqlRutina = "SELECT id_rutina
                  FROM rutinas
                  WHERE id_rutina = :id_rutina
                    AND id_usuario = :id_usuario";

    $stmtRutina = $conexion->prepare($sqlRutina);
    $stmtRutina->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtRutina->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtRutina->execute();

    $rutina = $stmtRutina->fetch(PDO::FETCH_ASSOC);

    if (!$rutina) {
        echo json_encode([
            'success' => false,
            'message' => 'La rutina no existe o no pertenece al usuario'
        ]);
        exit;
    }

    $sqlBuscarEjercicio = "SELECT id_ejercicio
                           FROM ejercicios
                           WHERE nombre = :nombre
                             AND grupo_muscular = :grupo_muscular
                           LIMIT 1";

    $stmtBuscarEjercicio = $conexion->prepare($sqlBuscarEjercicio);
    $stmtBuscarEjercicio->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmtBuscarEjercicio->bindParam(':grupo_muscular', $musculo, PDO::PARAM_STR);
    $stmtBuscarEjercicio->execute();

    $ejercicio = $stmtBuscarEjercicio->fetch(PDO::FETCH_ASSOC);

    if ($ejercicio) {
        $id_ejercicio = $ejercicio['id_ejercicio'];
    } else {

        $sqlInsertEjercicio = "INSERT INTO ejercicios (nombre, grupo_muscular, descripcion)
                               VALUES (:nombre, :grupo_muscular, :descripcion)";

        $stmtInsertEjercicio = $conexion->prepare($sqlInsertEjercicio);
        $stmtInsertEjercicio->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtInsertEjercicio->bindParam(':grupo_muscular', $musculo, PDO::PARAM_STR);
        $stmtInsertEjercicio->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmtInsertEjercicio->execute();

        $id_ejercicio = $conexion->lastInsertId();
    }

    $sqlExisteRelacion = "SELECT id_ejercicio
                          FROM rutina_ejercicios
                          WHERE id_rutina = :id_rutina
                            AND id_ejercicio = :id_ejercicio";

    $stmtExisteRelacion = $conexion->prepare($sqlExisteRelacion);
    $stmtExisteRelacion->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtExisteRelacion->bindParam(':id_ejercicio', $id_ejercicio, PDO::PARAM_INT);
    $stmtExisteRelacion->execute();

    $relacion = $stmtExisteRelacion->fetch(PDO::FETCH_ASSOC);

    if ($relacion) {
        echo json_encode([
            'success' => false,
            'message' => 'Este ejercicio ya está añadido a la rutina'
        ]);
        exit;
    }

    $sqlOrden = "SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente_orden
                 FROM rutina_ejercicios
                 WHERE id_rutina = :id_rutina";

    $stmtOrden = $conexion->prepare($sqlOrden);
    $stmtOrden->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtOrden->execute();

    $resultadoOrden = $stmtOrden->fetch(PDO::FETCH_ASSOC);
    $orden = $resultadoOrden['siguiente_orden'];

    $sqlRelacion = "INSERT INTO rutina_ejercicios (id_rutina, id_ejercicio, orden)
                    VALUES (:id_rutina, :id_ejercicio, :orden)";

    $stmtRelacion = $conexion->prepare($sqlRelacion);
    $stmtRelacion->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtRelacion->bindParam(':id_ejercicio', $id_ejercicio, PDO::PARAM_INT);
    $stmtRelacion->bindParam(':orden', $orden, PDO::PARAM_INT);
    $stmtRelacion->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Ejercicio añadido correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al añadir el ejercicio a la rutina'
    ]);
}