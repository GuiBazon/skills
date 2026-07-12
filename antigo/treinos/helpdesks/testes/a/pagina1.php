<?php
session_start();

echo 'Welcome to page #1';

$_SESSION['usuario_logado'] = 'Duda';
echo '<br /><a href="pagina2.php">pagina 2</a>';
