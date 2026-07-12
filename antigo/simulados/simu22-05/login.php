<?php

session_start();

if (isset($_POST["password"])){
    $password = $_POST["password"];
    if ($password == "admin"){
        $_SESSION["logado"] = true;
        header("location: companies.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <label for="password">Password</label>
        <input type="password" name="password">
        <button type="submit">Submit</button>
    </form>
</body>
</html>