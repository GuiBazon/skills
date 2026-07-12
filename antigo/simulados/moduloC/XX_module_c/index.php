<?php
require 'funcoes.php';
$base = '/XX_module_c';
$rota = trim(str_replace($base, '', $_SERVER['REQUEST_URI']), '/');

if ($rota === '') {
    listar('');  // raiz
} elseif (preg_match('#^heritages/(.+)$#', $rota, $m)) {
    $path = $m[1];
    $parts = explode('/', $path);
    $ultimo = end($parts);
    if (preg_match('/^\d{4}-\d{2}-\d{2}-/', $ultimo)) {
        $slug = array_pop($parts);
        $pasta = implode('/', $parts);
        exibirArtigo($pasta, $slug);
    } else {
        listar($path);
    }
} elseif (preg_match('#^tags/(.+)$#', $rota, $m)) {
    listarPorTag($m[1]);
} else {
    http_response_code(404);
    echo "404";
}