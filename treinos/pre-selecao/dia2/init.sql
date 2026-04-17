-- [mysql -u root -P 3307]

CREATE DATABASE crud_basico;
USE crud_basico;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);