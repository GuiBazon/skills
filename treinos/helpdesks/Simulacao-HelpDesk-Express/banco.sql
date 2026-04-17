create database if not exists seletiva_web_bazon;
use seletiva_web_bazon;

create table usuario (
    id int auto_increment primary key,
    login varchar(50) not null unique,
    senha varchar(255) not null,
    nome varchar(100) not null
);

insert into usuario (login, senha, nome) values ('admin', '123456', 'administrador do sistema');

create table chamado (
    id int auto_increment primary key,
    solicitante varchar(100) not null,
    email varchar(100) not null,
    equipamento varchar(100) not null,
    descricao text not null,
    data_registro datetime default current_timestamp
);