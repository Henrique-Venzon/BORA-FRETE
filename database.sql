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

-- ========================================
-- TABELA: notificacoes
-- ========================================
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('oferta', 'veiculo', 'mensagem', 'alerta', 'sucesso', 'info') DEFAULT 'info',
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_lida (lida),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir notificações de exemplo
INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem) VALUES
(1, 'sucesso', 'Bem-vindo ao BoraFrete!', 'Sua conta foi criada com sucesso. Complete seu perfil para começar.'),
(1, 'info', 'Nova oferta disponível', 'Há uma nova oferta de frete de São Paulo para Rio de Janeiro.'),
(2, 'oferta', 'Oferta aceita', 'Sua oferta foi visualizada por 5 motoristas.');

-- Adicionar colunas de geolocalização
ALTER TABLE usuarios
ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8),
ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8),
ADD COLUMN IF NOT EXISTS ultima_localizacao TIMESTAMP,
ADD COLUMN IF NOT EXISTS notif_ofertas BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS notif_mensagens BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS notif_sistema BOOLEAN DEFAULT TRUE;

-- Adicionar colunas de geolocalização para ofertas
ALTER TABLE ofertas
ADD COLUMN IF NOT EXISTS origem_lat DECIMAL(10, 8),
ADD COLUMN IF NOT EXISTS origem_lng DECIMAL(11, 8),
ADD COLUMN IF NOT EXISTS destino_lat DECIMAL(10, 8),
ADD COLUMN IF NOT EXISTS destino_lng DECIMAL(11, 8);

-- Tabela de mensagens/chat
CREATE TABLE IF NOT EXISTS mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    oferta_id INT,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE SET NULL,
    INDEX idx_remetente (remetente_id),
    INDEX idx_destinatario (destinatario_id),
    INDEX idx_oferta (oferta_id),
    INDEX idx_lida (lida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de matchings (Cargas compatíveis)
CREATE TABLE IF NOT EXISTS matchings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    motorista_id INT NOT NULL,
    score INT DEFAULT 0 COMMENT 'Pontuação de compatibilidade (0-100)',
    distancia_km DECIMAL(10, 2),
    notificado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE CASCADE,
    FOREIGN KEY (motorista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_match (oferta_id, motorista_id),
    INDEX idx_oferta (oferta_id),
    INDEX idx_motorista (motorista_id),
    INDEX idx_score (score DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistema de Avaliações/Rating
CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorista_id INT NOT NULL,
    avaliador_id INT NOT NULL,
    oferta_id INT,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    pontualidade INT CHECK (pontualidade >= 1 AND pontualidade <= 5),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (motorista_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (avaliador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE SET NULL,
    INDEX idx_motorista (motorista_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar colunas de rating e disponibilidade
ALTER TABLE usuarios
ADD COLUMN IF NOT EXISTS rating_medio DECIMAL(3, 2) DEFAULT 5.00,
ADD COLUMN IF NOT EXISTS total_avaliacoes INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_entregas INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_cancelamentos INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS disponivel_agora BOOLEAN DEFAULT FALSE;

-- Criar índice para busca de motoristas disponíveis
CREATE INDEX IF NOT EXISTS idx_disponivel_agora ON usuarios(disponivel_agora);
CREATE INDEX IF NOT EXISTS idx_rating ON usuarios(rating_medio DESC);
