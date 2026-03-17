-- ========================================
-- SISTEMA BORAFRETE - BANCO DE DADOS
-- ========================================

CREATE DATABASE IF NOT EXISTS borafrete CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE borafrete;

-- ========================================
-- TABELA: usuarios
-- ========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_perfil ENUM('transportadora', 'agenciador', 'motorista') NOT NULL,
    nome_razao_social VARCHAR(255) NOT NULL,
    documento_tipo ENUM('cpf', 'cnpj') NOT NULL,
    documento_numero VARCHAR(20) NOT NULL UNIQUE,
    ie VARCHAR(20) NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    mopp BOOLEAN DEFAULT FALSE,
    cnh_categorias SET('C', 'D', 'E') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_documento (documento_numero),
    INDEX idx_tipo_perfil (tipo_perfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: veiculos
-- ========================================
CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_veiculo ENUM('van', 'fiorino', '3/4', 'toco', 'truck', 'carreta', 'rodotrem') NOT NULL,
    tipo_carroceria VARCHAR(100) NULL,
    marca VARCHAR(100) NOT NULL,
    ano INT NOT NULL,
    placa_1 VARCHAR(10) NOT NULL,
    placa_2 VARCHAR(10) NULL,
    placa_3 VARCHAR(10) NULL,
    capacidade_peso DECIMAL(10,2) NOT NULL,
    capacidade_m3 DECIMAL(10,2) NULL,
    qtd_pallets INT NULL,
    foto VARCHAR(255) NULL,
    disponivel BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo_veiculo (tipo_veiculo),
    INDEX idx_disponivel (disponivel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: ofertas
-- ========================================
CREATE TABLE IF NOT EXISTS ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transportadora_id INT NOT NULL,
    origem_cidade VARCHAR(255) NOT NULL,
    origem_uf CHAR(2) NOT NULL,
    destino_cidade VARCHAR(255) NOT NULL,
    destino_uf CHAR(2) NOT NULL,
    data_carregamento DATE NOT NULL,
    hora_carregamento TIME NULL,
    data_entrega DATE NOT NULL,
    hora_entrega TIME NULL,
    tipo_veiculo ENUM('van', 'fiorino', '3/4', 'toco', 'truck', 'carreta', 'rodotrem') NOT NULL,
    tipo_carroceria VARCHAR(100) NULL,
    tipo_carga ENUM('seca', 'refrigerada', 'congelada', 'perigosa', 'quimica') NOT NULL,
    modelo_carga ENUM('caixas', 'maquinario', 'sacarias', 'racao', 'roupa', 'eletronicos') NOT NULL,
    peso DECIMAL(10,2) NOT NULL,
    cubagem DECIMAL(10,2) NULL,
    pallets INT NULL,
    frete_combinar BOOLEAN DEFAULT FALSE,
    valor_frete DECIMAL(10,2) NULL,
    pedagio_incluso BOOLEAN DEFAULT FALSE,
    tipo_pagamento VARCHAR(100) NULL,
    fator_pagamento VARCHAR(20) NULL,
    status ENUM('ativa', 'em_negociacao', 'fechada', 'cancelada') DEFAULT 'ativa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transportadora_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_transportadora (transportadora_id),
    INDEX idx_origem (origem_uf, origem_cidade),
    INDEX idx_destino (destino_uf, destino_cidade),
    INDEX idx_status (status),
    INDEX idx_data_carregamento (data_carregamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: password_resets
-- ========================================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiracao DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiracao (expiracao),
    INDEX idx_usado (usado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DADOS DE TESTE
-- ========================================

-- Usuario motorista (senha: 123456)
INSERT INTO usuarios (tipo_perfil, nome_razao_social, documento_tipo, documento_numero, email, senha, telefone, mopp, cnh_categorias)
VALUES ('motorista', 'João Silva', 'cpf', '12345678900', 'motorista@borafrete.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 98765-4321', TRUE, 'C,D,E');

-- Usuario transportadora (senha: 123456)
INSERT INTO usuarios (tipo_perfil, nome_razao_social, documento_tipo, documento_numero, ie, email, senha, telefone)
VALUES ('transportadora', 'Transportadora Rápida LTDA', 'cnpj', '12345678000190', '123456789', 'transportadora@borafrete.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 3333-4444');

-- Veiculos de teste
INSERT INTO veiculos (usuario_id, tipo_veiculo, tipo_carroceria, marca, ano, placa_1, capacidade_peso, capacidade_m3, qtd_pallets, disponivel)
VALUES (1, 'van', NULL, 'Mercedes Sprinter', 2021, 'BFA-1234', 1500.00, 15.00, 8, TRUE);

INSERT INTO veiculos (usuario_id, tipo_veiculo, tipo_carroceria, marca, ano, placa_1, capacidade_peso, capacidade_m3, qtd_pallets, disponivel)
VALUES (1, 'van', NULL, 'Renault Kangoo Z.E.', 2023, 'BFA-5678', 800.00, 4.50, 4, TRUE);

-- ========================================
-- FIM DO SCRIPT
-- ========================================
