<?php

// AULA DE PHP - BÁSICO
// Autor: Guilherme Bazon


// 1. VARIÁVEIS E TIPOS

function basico()
{
    echo "<h2>1. Variáveis e Tipos</h2><hr>";

    // Definição de variáveis
    $nome = "Bazon";      // String
    $idade = 16;          // Inteiro
    $altura = 1.76;       // Float
    $vivo = true;         // Boolean

    // Array (lista de valores)
    $ocupacoes = ["Programador", "Escoteiro", "Acordeonista"];
    // Forma alternativa:
    // $ocupacoes = array("Programador", "Escoteiro", "Acordeonista");

    // Exibição de dados
    print 'Meu nome é ' . $nome; // Concatenação de strings
    echo "<p>Tenho $idade anos, minha altura é $altura metros e estou vivo: " . ($vivo ? "Sim" : "Não") . ".</p>";

    // Exemplo de uso de var_dump (debug)
    echo "<p>Minhas ocupações são:</p><ul>";
    foreach ($ocupacoes as $ocupacao) {
        var_dump("<li>$ocupacao</li>"); // Mostra o tipo e valor da variável
    }
    echo "</ul>";

    $var1 = "Teste";
    $var2 = $var1;
}
basico();



// 2. OPERAÇÕES MATEMÁTICAS

function operacoes()
{
    echo "<h2>2. Operações Matemáticas</h2><hr>";

    $a = 10;
    $b = 3;

    $soma = $a + $b;
    $subtracao = $a - $b;
    $multiplicacao = $a * $b;
    $divisao = $a / $b;
    $modulo = $a % $b; // Resto da divisão

    echo "<p>Soma: $soma</p>";
    echo "<p>Subtração: $subtracao</p>";
    echo "<p>Multiplicação: $multiplicacao</p>";
    echo "<p>Divisão: $divisao</p>";
    echo "<p>Módulo: $modulo</p>";
}
operacoes();



// 3. ESTRUTURAS CONDICIONAIS

function condicional()
{
    echo "<h2>3. Estruturas Condicionais</h2><hr>";

    $numero = 10;

    // IF / ELSE
    if ($numero > 0) {
        echo "$numero é positivo.<br>";
    } elseif ($numero < 0) {
        echo "$numero é negativo.<br>";
    } else {
        echo "$numero é zero.<br>";
    }

    // SWITCH (vários casos possíveis)
    switch ($numero) {
        case 1:
            echo "Número é um.<br>";
            break;
        case 5:
            echo "Número é cinco.<br>";
            break;
        case 10:
            echo "Número é dez.<br>";
            break;
        default:
            echo "Número não é um, cinco ou dez.<br>";
            break;
    }
}
condicional();



// 4. ESTRUTURAS DE REPETIÇÃO

function loop()
{
    echo "<h2>4. Estruturas de Repetição</h2><hr>";

    // FOR: repete até a condição ser falsa
    echo "<h3>For</h3>";
    for ($i = 1; $i <= 5; $i++) {
        echo "Número: $i<br>";
    }

    // WHILE: verifica a condição antes de executar
    echo "<h3>While</h3>";
    $contador = 1;
    while ($contador <= 5) {
        echo "Contador: $contador<br>";
        $contador++;
    }

    // DO-WHILE: executa primeiro, verifica depois
    echo "<h3>Do-While</h3>";
    $contador = 1;
    do {
        echo "Do-While Contador: $contador<br>";
        $contador++;
    } while ($contador <= 5);

    echo "<p><i>O Do-While sempre executa pelo menos uma vez, mesmo se a condição for falsa.</i></p>";
}
loop();



// 5. RESUMO FINAL

echo "<hr><h2>Resumo</h2>";
echo "<p>Nessa aula vimos:</p>";
echo "<ul>
        <li>Exibição de informações com print, echo e var_dump</li>
        <li>Declaração de variáveis e tipos de dados</li>
        <li>Operações matemáticas básicas</li>
        <li>Estruturas condicionais (if, else, switch)</li>
        <li>Estruturas de repetição (for, while, do-while)</li>
      </ul>";

