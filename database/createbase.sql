CREATE DATABASE IF NOT EXISTS atendelab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE atendelab;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    perfil     ENUM('admin', 'atendente')  DEFAULT 'atendente',
    status     ENUM('ativo', 'inativo')    DEFAULT 'ativo',
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pessoas (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    cpf        VARCHAR(20)  UNIQUE,
    telefone   VARCHAR(20) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    curso      VARCHAR(100),
    periodo    VARCHAR(100),
    status     ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tipos_atendimentos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(255) NOT NULL,
    descricao  TEXT,
    status     ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

CREATE TABLE IF NOT EXISTS atendimentos (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    pessoa_id           INT  NOT NULL,
    usuario_id          INT  NOT NULL,
    tipo_atendimento_id INT  NOT NULL,
    data_atendimento    DATE NOT NULL,
    hora_atendimento    TIME,
    descricao           TEXT,
    observacao          TEXT,
    status              ENUM('aberto', 'em_andamento', 'concluido') DEFAULT 'aberto',
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pessoa_id)           REFERENCES pessoas(id),
    FOREIGN KEY (usuario_id)          REFERENCES usuarios(id),
    FOREIGN KEY (tipo_atendimento_id) REFERENCES tipos_atendimentos(id)
);