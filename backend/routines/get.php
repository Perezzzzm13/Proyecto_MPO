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
$id_rutina = $_GET['id'] ?? '';

if ($id_rutina === '' || !is_numeric($id_rutina)) {
    echo json_encode([
        'success' => false,
        'message' => 'Id de rutina no válido'
    ]);
    exit;
}

try {
    $sqlRutina = "SELECT id_rutina, nombre, descripcion
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

    $sqlEjercicios = "SELECT 
                        e.id_ejercicio,
                        e.nombre,
                        e.grupo_muscular,
                        e.descripcion,
                        re.orden
                      FROM rutina_ejercicios re
                      INNER JOIN ejercicios e 
                        ON re.id_ejercicio = e.id_ejercicio
                      WHERE re.id_rutina = :id_rutina
                      ORDER BY re.orden ASC";

    $stmtEjercicios = $conexion->prepare($sqlEjercicios);
    $stmtEjercicios->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
    $stmtEjercicios->execute();

    $ejercicios = $stmtEjercicios->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'rutina' => $rutina,
        'ejercicios' => $ejercicios
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener la rutina'
    ]);
}