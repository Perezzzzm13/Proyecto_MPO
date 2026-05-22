<?php
require_once '../config/database.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');

exigirAdmin();

$data = obtenerJson();
$idUsuario = (int) ($data['id_usuario'] ?? 0);
$rol = trim($data['rol'] ?? '');
$idActual = (int) $_SESSION['id_usuario'];

if ($idUsuario <= 0 || !in_array($rol, ['admin', 'usuario'], true)) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos invalidos para actualizar el rol.'
    ]);
    exit;
}

if ($idUsuario === $idActual && $rol !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'No puedes quitarte tu propio rol de admin.'
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

    if ($usuario['rol'] === 'admin' && $rol === 'usuario' && contarAdmins($conexion) <= 1) {
        echo json_encode([
            'success' => false,
            'message' => 'No puedes dejar la aplicacion sin administradores.'
        ]);
        exit;
    }

    $stmt = $conexion->prepare('UPDATE usuarios SET rol = :rol WHERE id_usuario = :id_usuario');
    $stmt->execute([
        ':rol' => $rol,
        ':id_usuario' => $idUsuario
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Rol actualizado correctamente.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error al actualizar el rol.'
    ]);
}
