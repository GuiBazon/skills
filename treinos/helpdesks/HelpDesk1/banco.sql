create database seletiva_web_bazon;

use seletiva_web_bazon;

create table
    usuario (
        id_usuario int auto_increment primary key,
        login varchar(255) not null unique,
        senha varchar(255) not null,
        nome varchar(255) not null
    );

insert into
    usuario (login, senha, nome)
values
    ("adm", "123", "usuario manual");

create table
    chamado (
        id_solicitante int auto_increment primary key,
        solicitante varchar(255) not null,
        email varchar(255) not null,
        equipamento varchar(255),
        descricao text,
        data_registro datetime default current_timestamp
    );