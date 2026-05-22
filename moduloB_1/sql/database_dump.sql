CREATE DATABASE skills17;
USE skills17;

CREATE TABLE companies (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  address text DEFAULT NULL,
  phone varchar(50) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  owner_name varchar(255) DEFAULT NULL,
  owner_phone varchar(50) DEFAULT NULL,
  owner_email varchar(255) DEFAULT NULL,
  contact_name varchar(255) DEFAULT NULL,
  contact_phone varchar(50) DEFAULT NULL,
  contact_email varchar(255) DEFAULT NULL,
  deactivated tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
  id int(11) NOT NULL AUTO_INCREMENT,
  gtin varchar(14) NOT NULL,
  name_en varchar(255) NOT NULL,
  name_fr varchar(255) NOT NULL,
  description_en text DEFAULT NULL,
  description_fr text DEFAULT NULL,
  brand varchar(255) DEFAULT NULL,
  country_of_origin varchar(100) DEFAULT NULL,
  gross_weight decimal(8,2) DEFAULT NULL,
  net_weight decimal(8,2) DEFAULT NULL,
  weight_unit varchar(10) DEFAULT NULL,
  image_path varchar(255) DEFAULT NULL,
  hidden tinyint(1) NOT NULL DEFAULT 0,
  company_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY gtin (gtin),
  KEY company_id (company_id),
  CONSTRAINT products_ibfk_1 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO companies (name, address, phone, email, owner_name, owner_phone, owner_email, contact_name, contact_phone, contact_email) VALUES
("Euro Expo", "Boulevard de l'Europe, 69680 Chassieu, France", "+33 1 41 56 78 00", "contact@euroexpo.fr", "Benjamin Smith", "+33 6 12 34 56 78", "b.smith@euroexpo.fr", "Marie Dubois", "+33 6 98 76 54 32", "m.dubois@euroexpo.fr");

INSERT INTO products (gtin, name_en, name_fr, description_en, description_fr, brand, country_of_origin, gross_weight, net_weight, weight_unit, company_id) VALUES
("3000123456789", "Organic Apple Juice", "Jus de pomme biologique", "Pressed from 100% fresh organic apples.", "Pressé à partir de pommes biologiques fraîches.", "Green Orchard", "France", 1.10, 1.00, "L", 1);