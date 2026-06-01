<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli('localhost', 'aluno', 'senai@604', 'seletiva_web_bazon');

$mysqli->set_charset('utf8mb4');

printf("Success... %s\n", $mysqli->host_info);
