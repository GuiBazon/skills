CREATE DATABASE biblioteca_db;

USE biblioteca_db;

CREATE TABLE
    usuarios (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255),
        username VARCHAR(100) NOT NULL,
        senha VARCHAR(255) NOT NULL
    );

CREATE TABLE
    livros (
        id_livro INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255),
        autor VARCHAR(255),
        quantidade_livros INT
    );