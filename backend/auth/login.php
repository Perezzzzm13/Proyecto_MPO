<?php 
session_start();

require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$identifier = trim($data['identifier'] ?? $data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($identifier === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Debe introducir el email o usuario y la contraseña'
    ]);
    exit;
}

try {
    $sql = 'SELECT id_usuario, nombre_usuario, nombre, email, password, rol
            FROM usuarios
            WHERE email = :identifier OR nombre_usuario = :identifier';

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':identifier' => $identifier
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$usuario) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario no existe.'
        ]);
        exit;
    }

    if (!password_verify($password, $usuario['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'La contraseña es incorrecta'
        ]);
        exit;
    }

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['rol'] = $usuario['rol'];

    echo json_encode([
        'success' => true,
        'message' => 'Login correcto.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ha ocurrido un error durante el login'
    ]);
}

?>
