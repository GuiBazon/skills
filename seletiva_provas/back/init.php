<?php
// ======================================================
// ARQUIVO ÚNICO DE APOIO. Toda página começa com:
//     require 'init.php';
// (ou require '../init.php' dentro de /api)
// ======================================================

session_start();

// ---- 1. Conexão (decore só isto, muda em nenhum outro lugar) ----
$pdo = new PDO('mysql:host=localhost;dbname=zoodata;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// ---- 2. Funções pequenas, reaproveitadas em tudo ----
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

function redirect($to) { header("Location: $to"); exit; }

function requireLogin() {
    if (!isset($_SESSION['user_id'])) redirect('login');
}

// slug: minúsculo, sem acento, espaço vira traço
function slug($s) {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $s));
    return trim($s, '-');
}

// número com no máximo 2 casas decimais (usa em tamanho e peso)
function dec2($v) { return number_format((float)$v, 2, '.', ''); }

// cor da legenda de risco
function riskColor($acronym) {
    return ['CR' => 'red', 'EN' => 'orange', 'VU' => '#d4b800', 'LC' => 'green'][$acronym] ?? '#999';
}

// texto do status
function statusLabel($s) {
    return ['em_exposicao' => 'Em exposição', 'fora_de_exibicao' => 'Fora de exibição', 'em_adaptacao' => 'Em adaptação'][$s] ?? $s;
}

// salva até 5 imagens dentro de uploads/animals/{slug}/ e devolve "1.jpg,2.png"
function uploadImages($files, $folderSlug, $startAt = 1) {
    if (empty($files['name'][0])) return '';
    $dir = __DIR__ . "/uploads/animals/$folderSlug";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $saved = [];
    $n = min(count($files['name']), 5);
    for ($i = 0; $i < $n; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $fname = ($startAt++) . ".$ext";
        move_uploaded_file($files['tmp_name'][$i], "$dir/$fname");
        $saved[] = $fname;
    }
    return implode(',', $saved);
}
