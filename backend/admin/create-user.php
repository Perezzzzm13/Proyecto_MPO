<?php
require_once '../config/database.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');

exigirAdmin();

$data = obtenerJson();

$nombreUsuario = trim($data['nombre_usuario'] ?? '');
$nombre = trim($data['nombre'] ?? '');
$apellidos = trim($data['apellidos'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$rol = trim($data['rol'] ?? 'usuario');

if ($nombreUsuario === '' || $nombre === '' || $email === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos obligatorios.'
    ]);
    exit;
}

if (!in_array($rol, ['admin', 'usuario'], true)) {
    echo json_encode([
        'success' => false,
        'message' => 'El rol seleccionado no es valido.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe introducir un email valido.'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 6 caracteres.'
    ]);
    exit;
}

try {
    $sql = 'SELECT id_usuario FROM usuarios WHERE email = :email OR nombre_usuario = :nombre_usuario';
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':nombre_usuario' => $nombreUsuario
    ]);

    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'El correo electronico o el usuario ya estan registrados.'
        ]);
        exit;
    }

    $sqlInsert = 'INSERT INTO usuarios (nombre_usuario, nombre, apellidos, email, password, rol)
                  VALUES (:nombre_usuario, :nombre, :apellidos, :email, :password, :rol)';

    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->execute([
        ':nombre_usuario' => $nombreUsuario,
        ':nombre' => $nombre,
        ':apellidos' => $apellidos,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':rol' => $rol
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado correctamente.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error al crear el usuario.'
    ]);
}
