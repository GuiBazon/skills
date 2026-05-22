<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    die('401 Unauthorized');
}
