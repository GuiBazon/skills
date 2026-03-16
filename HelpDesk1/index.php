<?php



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div>
        <form action="POST">
            <label for="login">Login:</label>
            <input type="text" name="login" required>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>