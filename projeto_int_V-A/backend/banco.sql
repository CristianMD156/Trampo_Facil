CREATE DATABASE trampofacil;
USE trampofacil;

-- Usuário (candidato ou empresa)
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    tipo ENUM('candidato', 'empresa'),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Candidato
CREATE TABLE candidato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    telefone VARCHAR(20),
    cidade VARCHAR(100),
    area_atuacao VARCHAR(100),
    resumo TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

-- Empresa
CREATE TABLE empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    razao_social VARCHAR(150),
    cnpj VARCHAR(20),
    descricao TEXT,
    site VARCHAR(150),
    cidade VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

-- Vagas
CREATE TABLE vaga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    titulo VARCHAR(150),
    descricao TEXT,
    salario DECIMAL(10,2),
    area VARCHAR(100),
    cidade VARCHAR(100),
    status ENUM('ativa', 'encerrada') DEFAULT 'ativa',
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresa(id)
);

-- Currículo
CREATE TABLE curriculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT,
    arquivo VARCHAR(255),
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidato(id)
);

-- Candidatura
CREATE TABLE candidatura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT,
    vaga_id INT,
    curriculo_id INT,
    status ENUM('enviado', 'em análise', 'aprovado', 'reprovado') DEFAULT 'enviado',
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidato(id),
    FOREIGN KEY (vaga_id) REFERENCES vaga(id),
    FOREIGN KEY (curriculo_id) REFERENCES curriculo(id)
);

-- Favoritas
CREATE TABLE vaga_favorita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT,
    vaga_id INT,
    UNIQUE(candidato_id, vaga_id),
    FOREIGN KEY (candidato_id) REFERENCES candidato(id),
    FOREIGN KEY (vaga_id) REFERENCES vaga(id)
);