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

$nombre = trim($data['nombre'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');
$id_usuario = $_SESSION['id_usuario'];

// Validaciones basicas antes de guardar para evitar datos vacios o demasiado largos.
if ($nombre === '') {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre de la rutina es obligatorio'
    ]);
    exit;
}

if (mb_strlen($nombre) > 50) {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre no puede superar los 50 caracteres'
    ]);
    exit;
}

if (mb_strlen($descripcion) > 255) {
    echo json_encode([
        'success' => false,
        'message' => 'La descripción no puede superar los 255 caracteres'
    ]);
    exit;
}

try {
    $sql = "INSERT INTO rutinas (nombre, descripcion, id_usuario)
            VALUES (:nombre, :descripcion, :id_usuario)";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Rutina creada correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la rutina'
    ]);
}
