CREATE DATABASE wsc_module_b;
USE wsc_module_b;

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    gtin VARCHAR(14) NOT NULL,
    name_en VARCHAR(255),
    name_fr VARCHAR(255),
    description_en TEXT,
    description_fr TEXT,
    image_path VARCHAR(255),
    is_hidden TINYINT(1) DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    UNIQUE INDEX idx_gtin (gtin)
);