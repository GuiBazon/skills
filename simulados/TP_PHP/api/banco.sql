CREATE DATABASE tp_php;
USE tp_php;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    username VARCHAR(75) UNIQUE NOT NULL, 
    senha VARCHAR(255) NOT NULL,          
    tipo ENUM('admin', 'regular') DEFAULT 'regular'
);

CREATE TABLE livros (
    id_livro INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    categoria VARCHAR(100),
    ano_publicacao INT,
    isbn VARCHAR(20),
    quantidade_estoque INT DEFAULT 0,
    descricao TEXT,
    disponivel TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE historico (
    id_historico INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_livro INT,
    tipo_transacao ENUM('emprestimo', 'devolucao') DEFAULT 'emprestimo',
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livros(id_livro) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, username, senha, tipo) VALUES 
('Administrador', 'admin', '123', 'admin'),
('Usuario Teste', 'user1', '123', 'regular');

INSERT INTO livros (titulo, autor, categoria, ano_publicacao, isbn, quantidade_estoque, disponivel) VALUES 
('O Senhor dos Anéis', 'J.R.R. Tolkien', 'Fantasia', 1954, '978-8533944749', 5, 1),
('Dom Casmurro', 'Machado de Assis', 'Clássico', 1899, '978-8572327428', 2, 1);