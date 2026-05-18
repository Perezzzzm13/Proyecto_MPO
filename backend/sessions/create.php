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
        'message' => 'No hay ejercicios para guardar'
    ]);
    exit;
}

try {
    // 1. Comprobar que la rutina pertenece al usuario
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

    // 2. Crear sesión
    $sqlSesion = "INSERT INTO sesiones (id_usuario, id_rutina)
                  VALUES (:id_usuario, :id_rutina)";

    $stmtSesion = $conexion->prepare($sqlSesion);
    $stmtSesion->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtSesion->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtSesion->execute();

    $id_sesion = $conexion->lastInsertId();

    // 3. Insertar ejercicios realizados
    $sqlSesionEjercicio = "INSERT INTO sesion_ejercicios 
                            (id_sesion, id_ejercicio, numero_serie, repeticiones, peso)
                           VALUES 
                            (:id_sesion, :id_ejercicio, :numero_serie, :repeticiones, :peso)";

    $stmtSesionEjercicio = $conexion->prepare($sqlSesionEjercicio);

    foreach ($ejercicios as $ejercicio) {
        $id_ejercicio = $ejercicio['id_ejercicio'] ?? '';
        $numero_serie = $ejercicio['numero_serie'] ?? '';
        $repeticiones = $ejercicio['repeticiones'] ?? null;
        $peso = $ejercicio['peso'] ?? null;

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
    }

    echo json_encode([
        'success' => true,
        'message' => 'Sesión guardada correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la sesión'
    ]);
}