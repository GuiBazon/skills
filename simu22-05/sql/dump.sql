create database backend22_05;
use backend22_05;

create table companies (
    company_id int auto_increment primary key,
    company_name varchar(150) not null,
    adress text default null,
    company_phone varchar(20) default null,
    company_email varchar(150) default null,
    owner_name varchar(150) default null,
    owner_phone varchar(20) default null,
    owner_email varchar(150) default null,
    contact_name varchar(150) default null,
    contact_phone varchar(20) default null,
    contact_email varchar(150) default null,
    is_active tinyint default 1
);

create table products (
    product_id int auto_increment primary key,
    product_name varchar(150) not null,
    product_name_fr varchar(150) not null,
    GTIN char(14) unique not null,
    description text default null,
    description_fr text default null,
    brand varchar(150) default null,
    country_of_origin varchar(25) default null,
    full_weight decimal(10,2) default null,
    liquid_weight decimal(9,2) default null,
    weight_unit varchar(15) default null,
    image_path varchar(255) DEFAULT "assets/uploads/",
    hidden tinyint default 1,
    fk_company_id int default null,
    foreign key (fk_company_id) references companies(company_id)
);

insert into companies(company_name) values ("Nome qualquer");

insert into products (product_name, product_name_fr, GTIN) values ("Nome ingles", "Nome frances", 1234567890123);