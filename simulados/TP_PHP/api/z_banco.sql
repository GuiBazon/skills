create database tp_php;

use tp_php;

create table
    usuarios (
        id_usuario int auto_increment primary key,
        nome varchar(255) not null,
        email varchar(255) unique not null,
        username varchar(75) unique not null,
        senha varchar(255) not null,
        tipo enum ("admin", "regular")
    );

insert into
    usuarios (nome, email, username, senha)
values
    ("Usuario teste", "u@u", "usernameteste", "a1234"),
    ("a", "a", "a", "a");

create table
    banco_livros (
        id_livro_banco int auto_increment primary key,
        titulo varchar(255) not null,
        autor varchar(255) not null,
        categoria varchar(255),
        ano_publicacao char(5),
        isbn char(13),
        quantidade_estoque varchar(10),
        descricao text,
        disponivel tinyint,
        created_at datetime
    );

create table
    livros (
        id_livros int auto_increment primary key,
        titulo varchar(255) not null,
        quantidade_livros varchar(10),
        autor varchar(255) not null
    );

INSERT INTO
    livros (titulo, quantidade_livros, autor)
values
    ('a', '1', 'a'),
    ('b', '2', 'b');

create table
    log (
        id_log int auto_increment primary key,
        id_usuario int,
        id_livro int,
        foreign key (id_usuario) references usuarios (id_usuario),
        foreign key (id_livro) references livros (id_livro)
    );