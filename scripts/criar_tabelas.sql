-- ==================================================
-- SCRIPT PARA CRIAR TABELAS NO PHPMYADMIN
-- Copie e cole este código no phpMyAdmin do InfinityFree
-- ==================================================

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de posts/artigos
CREATE TABLE IF NOT EXISTS posts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    conteudo TEXT NOT NULL,
    autor_id INT(11),
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    visualizacoes INT(11) DEFAULT 0,
    status ENUM('rascunho', 'publicado', 'arquivado') DEFAULT 'rascunho',
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_data (data_publicacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de comentários
CREATE TABLE IF NOT EXISTS comentarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) NOT NULL,
    usuario_id INT(11),
    comentario TEXT NOT NULL,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aprovado TINYINT(1) DEFAULT 0,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_post (post_id),
    INDEX idx_aprovado (aprovado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados de exemplo
INSERT INTO usuarios (nome, email, telefone) VALUES
('João Silva', 'joao@example.com', '(11) 98765-4321'),
('Maria Santos', 'maria@example.com', '(21) 91234-5678'),
('Pedro Oliveira', 'pedro@example.com', '(31) 99876-5432');

INSERT INTO posts (titulo, conteudo, autor_id, status) VALUES
('Bem-vindo ao nosso site', 'Este é o primeiro post do nosso site. Esperamos que você goste!', 1, 'publicado'),
('Dicas de PHP e MySQL', 'Aqui estão algumas dicas importantes para trabalhar com PHP e MySQL...', 2, 'publicado'),
('Hospedagem no InfinityFree', 'Tutorial sobre como hospedar seu site gratuitamente.', 1, 'rascunho');
