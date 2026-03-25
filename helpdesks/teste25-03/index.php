<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("localhost", "aluno", "senai@604", "seletiva_web_bazon");

/* Create table doesn't return a resultset */
$mysqli->query("INSERT INTO chamado ('login', 'senha', 'nome') values ('algum', '123', 'seila')");
printf("Table myCity successfully created.\n");

/* Select queries return a resultset */
$result = $mysqli->query("SELECT nome FROM chamado LIMIT 10");
printf("Select returned %d rows.\n", $result->num_rows);

/* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
$result = $mysqli->query("SELECT * FROM chamado", MYSQLI_USE_RESULT);

/* Note, that we can't execute any functions which interact with the
    server until all records have been fully retrieved or the result
    set was closed. All calls will return an 'out of sync' error */
$mysqli->query("SET @a:='this will not work'");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="POST">
        <input name="campo1">
        <input name="campo2">
        <button type="submit">post</button>
    </form>
</body>

</html>