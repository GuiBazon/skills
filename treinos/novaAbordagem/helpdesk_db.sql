create database helpdesk_db;

use helpdesk_db;

create table
    usuarios (
        id_usuario int auto_increment primary key,
        login varchar(55) unique,
        senha varchar(255),
        nome varchar(255)
    );

insert into
    usuarios (login, senha, nome)
values
    ("UserTeste", "a1234", "nomeTeste");

create table
    chamados (
        id_chamado int auto_increment primary key,
        solicitante varchar(255),
        email varchar(255),
        equipamento varchar(255),
        descricao text,
        data_registro datetime default now ()
    );