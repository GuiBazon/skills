<?php

session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("localhost", "aluno", "senai@604", "teste_bazon");

$mysqli->query("SELECT * FROM usuario");
printf("GET * FROM 'usuario'.\n");

$result = $mysqli->query("SELECT * FROM usuario");
echo "$result";

?>