<?php
require_once '../config/database.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');

exigirAdmin();

$data = obtenerJson();
$idUsuario = (int) ($data['id_usuario'] ?? 0);
$idActual = (int) $_SESSION['id_usuario'];

if ($idUsuario <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario invalido.'
    ]);
    exit;
}

if ($idUsuario === $idActual) {
    echo json_encode([
        'success' => false,
        'message' => 'No puedes eliminar tu propia cuenta desde aqui.'
    ]);
    exit;
}

try {
    $stmtUsuario = $conexion->prepare('SELECT id_usuario, rol FROM usuarios WHERE id_usuario = :id_usuario');
    $stmtUsuario->execute([':id_usuario' => $idUsuario]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario no existe.'
        ]);
        exit;
    }

    if ($usuario['rol'] === 'admin' && contarAdmins($conexion) <= 1) {
        echo json_encode([
            'success' => false,
            'message' => 'No puedes eliminar el ultimo administrador.'
        ]);
        exit;
    }

    $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id_usuario = :id_usuario');
    $stmt->execute([':id_usuario' => $idUsuario]);

    echo json_encode([
        'success' => true,
        'message' => 'Usuario eliminado correctamente.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error al eliminar el usuario.'
    ]);
}
