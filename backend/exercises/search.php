<?php
header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

$apiKey = 'VoTD0oOfzfF7kyrPMRcF8hwPWWfvzXDUWE9ptX67';

$name = trim($_GET['name'] ?? '');
$muscle = trim($_GET['muscle'] ?? '');

$params = [];

if ($name !== '') {
    $params['name'] = $name;
}

if ($muscle !== '') {
    $params['muscle'] = $muscle;
}

$url = 'https://api.api-ninjas.com/v1/exercises';

if (!empty($params)) {
    $url .= '?' . http_build_query($params);
}

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'X-Api-Key: ' . $apiKey
    ]
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con la API de ejercicios'
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener ejercicios desde la API externa'
    ]);
    exit;
}

$ejercicios = json_decode($response, true);

echo json_encode([
    'success' => true,
    'ejercicios' => $ejercicios
]);