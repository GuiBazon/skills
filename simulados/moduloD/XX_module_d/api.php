<?php
/**
 * API Mock - Lyon Mobile Web Service
 * WorldSkills Module D
 *
 * Endpoints:
 *   /carparks.json  -> Lista de estacionamentos (D2)
 *   /events.json    -> Lista de eventos com paginação (D3)
 *   /weather.json   -> Previsão do tempo 7 dias (D4)
 *
 * Uso: /XX_module_d/api.php/{endpoint}
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// --- Roteamento via PATH_INFO ---
$route = '';
if (!empty($_SERVER['PATH_INFO'])) {
    $route = trim($_SERVER['PATH_INFO'], '/');
} else {
    // Fallback: extrair rota do REQUEST_URI
    $uri    = $_SERVER['REQUEST_URI'] ?? '';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $path   = parse_url($uri, PHP_URL_PATH);
    if (strpos($path, $script) === 0) {
        $route = trim(substr($path, strlen($script)), '/');
    }
}

switch ($route) {
    case 'carparks.json': handleCarparks(); break;
    case 'events.json':   handleEvents();   break;
    case 'weather.json':  handleWeather();  break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint não encontrado', 'route' => $route]);
}

// ===========================================================================
// D2 – Carpark (3.0 pts)
// ===========================================================================
function handleCarparks(): void {
    $carparks = [
        [
            'id'        => 1,
            'name'      => 'Parking Part-Dieu',
            'available' => 34,
            'total'     => 120,
            'latitude'  => 45.7605,
            'longitude' => 4.8590
        ],
        [
            'id'        => 2,
            'name'      => 'Parking Bellecour',
            'available' => 12,
            'total'     => 80,
            'latitude'  => 45.7578,
            'longitude' => 4.8320
        ],
        [
            'id'        => 3,
            'name'      => 'Parking Confluence',
            'available' => 89,
            'total'     => 150,
            'latitude'  => 45.7400,
            'longitude' => 4.8180
        ],
        [
            'id'        => 4,
            'name'      => 'Parking Gare Part-Dieu',
            'available' => 55,
            'total'     => 200,
            'latitude'  => 45.7609,
            'longitude' => 4.8598
        ],
        [
            'id'        => 5,
            'name'      => 'Parking Hôtel de Ville',
            'available' => 8,
            'total'     => 60,
            'latitude'  => 45.7675,
            'longitude' => 4.8335
        ]
    ];

    echo json_encode($carparks, JSON_UNESCAPED_UNICODE);
}

// ===========================================================================
// D3 – Events (3.25 pts)
// ===========================================================================
function handleEvents(): void {
    $page          = max(1, intval($_GET['page'] ?? 1));
    $beginningDate = $_GET['beginning_date'] ?? null;
    $endingDate    = $_GET['ending_date'] ?? null;
    $perPage       = 5;

    $events = getMockEvents();

    // Filtros por data
    if ($beginningDate) {
        $events = array_values(array_filter($events, function ($e) use ($beginningDate) {
            return $e['date'] >= $beginningDate;
        }));
    }
    if ($endingDate) {
        $events = array_values(array_filter($events, function ($e) use ($endingDate) {
            return $e['date'] <= $endingDate;
        }));
    }

    $total      = count($events);
    $totalPages = max(1, ceil($total / $perPage));
    $page       = min($page, $totalPages);
    $offset     = ($page - 1) * $perPage;
    $pageData   = array_slice($events, $offset, $perPage);

    // Montar URLs de paginação
    $baseUrl    = '/XX_module_d/api.php/events.json';
    $queryParts = [];
    if ($beginningDate) $queryParts[] = 'beginning_date=' . urlencode($beginningDate);
    if ($endingDate)    $queryParts[] = 'ending_date=' . urlencode($endingDate);
    $qs = $queryParts ? implode('&', $queryParts) . '&' : '';

    echo json_encode([
        'data' => $pageData,
        'pages' => [
            'next' => ($page < $totalPages && !empty($pageData))
                ? $baseUrl . '?' . $qs . 'page=' . ($page + 1)
                : null,
            'prev' => ($page > 1)
                ? $baseUrl . '?' . $qs . 'page=' . ($page - 1)
                : null,
        ]
    ], JSON_UNESCAPED_UNICODE);
}

function getMockEvents(): array {
    return [
        ['id' => 1,  'title' => 'Fête des Lumières',         'date' => '2026-06-01', 'image' => 'https://picsum.photos/seed/lyon1/300/200'],
        ['id' => 2,  'title' => 'Concert Orchestre National', 'date' => '2026-06-05', 'image' => 'https://picsum.photos/seed/lyon2/300/200'],
        ['id' => 3,  'title' => 'Exposition Peinture Moderne','date' => '2026-06-08', 'image' => 'https://picsum.photos/seed/lyon3/300/200'],
        ['id' => 4,  'title' => 'Festival Jazz à Lyon',       'date' => '2026-06-12', 'image' => 'https://picsum.photos/seed/lyon4/300/200'],
        ['id' => 5,  'title' => 'Salon du Livre',             'date' => '2026-06-15', 'image' => 'https://picsum.photos/seed/lyon5/300/200'],
        ['id' => 6,  'title' => 'Course de Lyon',             'date' => '2026-06-18', 'image' => 'https://picsum.photos/seed/lyon6/300/200'],
        ['id' => 7,  'title' => 'Marché de Noël',             'date' => '2026-06-20', 'image' => 'https://picsum.photos/seed/lyon7/300/200'],
        ['id' => 8,  'title' => 'Festival Lumière',           'date' => '2026-06-22', 'image' => 'https://picsum.photos/seed/lyon8/300/200'],
        ['id' => 9,  'title' => 'Spectacle de Danse',         'date' => '2026-06-25', 'image' => 'https://picsum.photos/seed/lyon9/300/200'],
        ['id' => 10, 'title' => 'Conférence Tech',            'date' => '2026-06-28', 'image' => 'https://picsum.photos/seed/lyon10/300/200'],
        ['id' => 11, 'title' => 'Tournoi de Tennis',          'date' => '2026-07-01', 'image' => 'https://picsum.photos/seed/lyon11/300/200'],
        ['id' => 12, 'title' => 'Opéra Carmen',               'date' => '2026-07-04', 'image' => 'https://picsum.photos/seed/lyon12/300/200'],
        ['id' => 13, 'title' => 'Salon de la Gastronomie',    'date' => '2026-07-07', 'image' => 'https://picsum.photos/seed/lyon13/300/200'],
        ['id' => 14, 'title' => 'Festival de Cirque',         'date' => '2026-07-10', 'image' => 'https://picsum.photos/seed/lyon14/300/200'],
        ['id' => 15, 'title' => 'Nuit des Musées',            'date' => '2026-07-13', 'image' => 'https://picsum.photos/seed/lyon15/300/200'],
        ['id' => 16, 'title' => 'Marathon de Lyon',           'date' => '2026-07-16', 'image' => 'https://picsum.photos/seed/lyon16/300/200'],
        ['id' => 17, 'title' => 'Fête de la Musique',         'date' => '2026-07-19', 'image' => 'https://picsum.photos/seed/lyon17/300/200'],
        ['id' => 18, 'title' => 'Exposition Photo',           'date' => '2026-07-22', 'image' => 'https://picsum.photos/seed/lyon18/300/200'],
    ];
}

// ===========================================================================
// D4 – Weather (1.75 pts)
// ===========================================================================
function handleWeather(): void {
    $weather = [
        ['date' => '2026-06-01', 'temp' => 22, 'condition' => 'Ensolarado',
         'svgPath' => 'M50 20 L55 35 L70 35 L58 45 L63 60 L50 50 L37 60 L42 45 L30 35 L45 35 Z'],
        ['date' => '2026-06-02', 'temp' => 19, 'condition' => 'Nublado',
         'svgPath' => 'M30 55 Q40 35 55 40 Q65 30 80 45 Q95 40 90 60 L30 60 Z'],
        ['date' => '2026-06-03', 'temp' => 17, 'condition' => 'Chuvoso',
         'svgPath' => 'M25 50 Q35 30 55 40 Q65 25 85 45 Q95 40 95 60 L25 60 Z M40 65 L40 75 M50 65 L50 78 M60 65 L60 75 M70 65 L70 78'],
        ['date' => '2026-06-04', 'temp' => 24, 'condition' => 'Ensolarado',
         'svgPath' => 'M50 20 L55 35 L70 35 L58 45 L63 60 L50 50 L37 60 L42 45 L30 35 L45 35 Z'],
        ['date' => '2026-06-05', 'temp' => 20, 'condition' => 'Parcialmente Nublado',
         'svgPath' => 'M50 20 L55 35 L70 35 L58 45 L63 60 L50 50 L37 60 L42 45 L30 35 L45 35 Z M65 40 Q80 30 90 50 Q95 45 90 60 L65 60 Z'],
        ['date' => '2026-06-06', 'temp' => 15, 'condition' => 'Tempestade',
         'svgPath' => 'M20 55 Q30 35 50 45 Q60 25 80 50 Q90 40 90 60 L20 60 Z M45 62 L45 80 M55 62 L55 80 M65 62 L65 80 M75 62 L75 80 M85 62 L85 80'],
        ['date' => '2026-06-07', 'temp' => 21, 'condition' => 'Ensolarado',
         'svgPath' => 'M50 20 L55 35 L70 35 L58 45 L63 60 L50 50 L37 60 L42 45 L30 35 L45 35 Z'],
    ];

    echo json_encode($weather, JSON_UNESCAPED_UNICODE);
}
