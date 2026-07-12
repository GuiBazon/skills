CREATE DATABASE IF NOT EXISTS zoodata CHARACTER SET utf8mb4;
USE zoodata;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(50) UNIQUE,
    password VARCHAR(64)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE TABLE feed_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE TABLE extinction_risks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(50),
    acronym VARCHAR(5)
);

-- "images" guarda os nomes dos arquivos separados por vírgula: "1.jpg,2.jpg"
-- Evita criar uma tabela extra só para isso -> menos JOIN, menos código pra decorar.
CREATE TABLE animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE,
    scientific_name VARCHAR(100) UNIQUE,
    description VARCHAR(250),
    size DECIMAL(6,2),
    weight DECIMAL(6,2),
    feed_class_id INT,
    extinction_risk_id INT,
    operation_status ENUM('em_exposicao','fora_de_exibicao','em_adaptacao') DEFAULT 'em_exposicao',
    category_id INT,
    images VARCHAR(255) DEFAULT '',
    visits INT DEFAULT 0
);

-- Mesma estrutura da tabela animals, só pra guardar o "histórico" de removidos.
CREATE TABLE excluded_animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    scientific_name VARCHAR(100),
    description VARCHAR(250),
    size DECIMAL(6,2),
    weight DECIMAL(6,2),
    feed_class_id INT,
    extinction_risk_id INT,
    operation_status VARCHAR(30),
    category_id INT,
    images VARCHAR(255),
    visits INT
);

INSERT INTO categories (name) VALUES ('Mamífero'),('Ave'),('Réptil'),('Anfíbio'),('Peixe');
INSERT INTO feed_classes (name) VALUES ('Carnívoro'),('Herbívoro'),('Onívoro');
INSERT INTO extinction_risks (description, acronym) VALUES
 ('Criticamente em perigo','CR'),('Em perigo','EN'),('Vulnerável','VU'),('Seguro','LC');
