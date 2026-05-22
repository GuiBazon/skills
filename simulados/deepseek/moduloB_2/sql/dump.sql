CREATE DATABASE skill17;
USE skill17;

CREATE TABLE companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  deactivated TINYINT(1) DEFAULT 0
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gtin VARCHAR(14) UNIQUE NOT NULL,
  name_en VARCHAR(255) NOT NULL,
  name_fr VARCHAR(255) NOT NULL,
  description_en TEXT,
  description_fr TEXT,
  hidden TINYINT(1) DEFAULT 0,
  company_id INT,
  image_path VARCHAR(255),
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- Índice exigido no CIS
CREATE INDEX idx_gtin ON products(gtin);

-- Dados mínimos para teste
INSERT INTO companies (name) VALUES ('Empresa Teste');
INSERT INTO products (gtin, name_en, name_fr, company_id) VALUES ('1234567890123', 'Produto EN', 'Produit FR', 1);