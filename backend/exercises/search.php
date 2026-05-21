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

$apiKey = 'pgJG4gzNBC69tgM720nlMUjwl0miPTgn5ZGIZDGA';

$name = trim($_GET['name'] ?? '');
$muscle = trim($_GET['muscle'] ?? '');

$params = [];

if ($name !== '') {
    $params['name'] = $name;
}

if ($muscle !== '') {
    $params['muscle'] = $muscle;
}

if (empty($params)) {
    echo json_encode([
        'success' => true,
        'ejercicios' => filtrarEjerciciosLocales($name, $muscle)
    ]);
    exit;
}

$url = 'https://api.api-ninjas.com/v1/exercises';

$url .= '?' . http_build_query($params);

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
        'success' => true,
        'ejercicios' => filtrarEjerciciosLocales($name, $muscle)
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'success' => true,
        'ejercicios' => filtrarEjerciciosLocales($name, $muscle)
    ]);
    exit;
}

$ejercicios = json_decode($response, true);

echo json_encode([
    'success' => true,
    'ejercicios' => $ejercicios
]);

function filtrarEjerciciosLocales($name, $muscle) {
    $ejercicios = [
        [
            'name' => 'Rickshaw Carry',
            'type' => 'strongman',
            'muscle' => 'forearms',
            'difficulty' => 'intermediate'
        ],
        [
            'name' => 'Single-Leg Press',
            'type' => 'strength',
            'muscle' => 'quadriceps',
            'difficulty' => 'intermediate'
        ],
        [
            'name' => 'Landmine twist',
            'type' => 'strength',
            'muscle' => 'abdominals',
            'difficulty' => 'intermediate'
        ],
        [
            'name' => 'Dumbbell front raise to lateral raise',
            'type' => 'strength',
            'muscle' => 'shoulders',
            'difficulty' => 'intermediate'
        ],
        [
            'name' => 'Palms-down wrist curl over bench',
            'type' => 'strength',
            'muscle' => 'forearms',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Atlas Stones',
            'type' => 'strongman',
            'muscle' => 'lower_back',
            'difficulty' => 'expert'
        ],
        [
            'name' => 'Clean from Blocks',
            'type' => 'olympic_weightlifting',
            'muscle' => 'quadriceps',
            'difficulty' => 'expert'
        ],
        [
            'name' => 'Incline Hammer Curls',
            'type' => 'strength',
            'muscle' => 'biceps',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Side Bridge',
            'type' => 'strength',
            'muscle' => 'abdominals',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Smith Machine Calf Raise',
            'type' => 'strength',
            'muscle' => 'calves',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Bench Press',
            'type' => 'strength',
            'muscle' => 'chest',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Pullups',
            'type' => 'strength',
            'muscle' => 'lats',
            'difficulty' => 'intermediate'
        ],
        [
            'name' => 'Triceps Pushdown',
            'type' => 'strength',
            'muscle' => 'triceps',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Leg Curl',
            'type' => 'strength',
            'muscle' => 'hamstrings',
            'difficulty' => 'beginner'
        ],
        [
            'name' => 'Hip Thrust',
            'type' => 'strength',
            'muscle' => 'glutes',
            'difficulty' => 'beginner'
        ]
    ];

    $name = strtolower(trim($name));
    $muscle = strtolower(trim($muscle));

    return array_values(array_filter($ejercicios, function($ejercicio) use ($name, $muscle) {
        $coincideNombre = $name === '' || str_contains(strtolower($ejercicio['name']), $name);
        $coincideMusculo = $muscle === '' || strtolower($ejercicio['muscle']) === $muscle;

        return $coincideNombre && $coincideMusculo;
    }));
}
