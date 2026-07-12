<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ---------- Auth ----------
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// ---------- Helpers ----------
function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify($text) {
    $text = trim($text);
    $text = mb_strtolower($text, 'UTF-8');
    $map = [
        'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ç'=>'c','ñ'=>'n'
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// ---------- Domínio ----------
function risk_color($acronym) {
    $colors = [
        'CR' => '#e53935', // Criticamente em perigo - vermelho
        'EN' => '#fb8c00', // Em perigo - laranja
        'VU' => '#fdd835', // Vulnerável - amarelo
        'LC' => '#43a047', // Seguro - verde
    ];
    return $colors[$acronym] ?? '#999';
}

function status_label($status) {
    $labels = [
        'em_exposicao'    => 'Em exposição',
        'fora_de_exibicao'=> 'Fora de exibição',
        'em_adaptacao'    => 'Em adaptação',
    ];
    return $labels[$status] ?? $status;
}

// ---------- Upload de imagens ----------
// Salva até 5 imagens em uploads/animals/{slug}/N.ext e retorna array de filenames
function save_animal_images($files, $slug) {
    $dir = __DIR__ . '/../uploads/animals/' . $slug;
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $saved = [];
    if (empty($files['name'][0])) return $saved;

    $count = min(count($files['name']), 5);
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        $filename = ($i + 1) . '.' . $ext;
        move_uploaded_file($files['tmp_name'][$i], $dir . '/' . $filename);
        $saved[] = $filename;
    }
    return $saved;
}

function move_animal_folder($slug) {
    $base = __DIR__ . '/../uploads/animals/';
    $from = $base . $slug;
    $to   = $base . 'excluded_animals/' . $slug;
    if (is_dir($from)) {
        if (!is_dir($base . 'excluded_animals')) mkdir($base . 'excluded_animals', 0777, true);
        rename($from, $to);
    }
}
