<?php
require_once __DIR__ . '/includes/functions.php';
$_SESSION = [];
session_destroy();
redirect('login');
