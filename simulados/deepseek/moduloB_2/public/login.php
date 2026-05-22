<?php
session_start();
if ($_POST['password'] ?? '' === 'admin') {
    $_SESSION['logged_in'] = true;
    header('Location: companies.php');
    exit;
}
?>
<form method="post">
    <input type="password" name="password">
    <button>Login</button>
</form>