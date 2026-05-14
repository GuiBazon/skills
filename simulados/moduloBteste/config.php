<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=wsc_module_b", "root", "");

function checkAuth()
{
    if (!isset($_SESSION['admin'])) {
        http_response_code(401);
        echo "Não autorizado";
        exit;
    }
};
