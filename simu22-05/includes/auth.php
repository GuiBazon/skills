<?php

session_start();

if ($_SESSION["logado"] == !true){
    http_response_code(401);
    exit("Não autorizado");
}

