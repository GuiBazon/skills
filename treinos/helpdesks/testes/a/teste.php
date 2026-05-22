<?php
$nome_digitado = $_POST["nome_digitado"];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'aluno', 'senai@604', 'banco_teste');
$mysqli->set_charset('utf8mb4');
printf("Conectou com sucesso\n");

$mysqli->query("INSERT INTO tabela_teste (nome_digitado) VALUES ('$nome_digitado')");

$query = "SELECT id, nome_digitado FROM tabela_teste ORDER BY id DESC";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) {
    printf("%s (%s)\n", $row["id"], $row["nome_digitado"]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST">
        <input type="text" class="nome_digitado" name="nome_digitado">
        <button type="submit">post</button>
    </form>
</body>

</html>