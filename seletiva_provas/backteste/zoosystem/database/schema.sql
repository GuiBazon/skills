CREATE DATABASE IF NOT EXISTS zoodata CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zoodata;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE feed_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE extinction_risks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(50) NOT NULL,
    acronym VARCHAR(5) NOT NULL
);

CREATE TABLE animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    scientific_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(250) NOT NULL,
    size DECIMAL(6,2) NOT NULL,
    weight DECIMAL(6,2) NOT NULL,
    feed_class_id INT NOT NULL,
    extinction_risk_id INT NOT NULL,
    operation_status ENUM('em_exposicao','fora_de_exibicao','em_adaptacao') NOT NULL DEFAULT 'em_exposicao',
    category_id INT NOT NULL,
    visits INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feed_class_id) REFERENCES feed_classes(id),
    FOREIGN KEY (extinction_risk_id) REFERENCES extinction_risks(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE animal_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    filename VARCHAR(150) NOT NULL,
    position INT NOT NULL DEFAULT 1,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE excluded_animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT,
    name VARCHAR(100),
    scientific_name VARCHAR(100),
    description VARCHAR(250),
    size DECIMAL(6,2),
    weight DECIMAL(6,2),
    feed_class_id INT,
    extinction_risk_id INT,
    operation_status VARCHAR(30),
    category_id INT,
    visits INT,
    images TEXT,
    excluded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seeds básicos
INSERT INTO categories (name) VALUES ('Mamífero'), ('Ave'), ('Réptil'), ('Anfíbio'), ('Peixe');
INSERT INTO feed_classes (name) VALUES ('Carnívoro'), ('Herbívoro'), ('Onívoro');
INSERT INTO extinction_risks (description, acronym) VALUES
    ('Criticamente em perigo', 'CR'),
    ('Em perigo', 'EN'),
    ('Vulnerável', 'VU'),
    ('Seguro', 'LC');
