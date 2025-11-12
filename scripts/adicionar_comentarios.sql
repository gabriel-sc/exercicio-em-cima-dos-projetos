-- ==================================================
-- SCRIPT PARA ADICIONAR SISTEMA DE COMENTÁRIOS
-- Execute este script no phpMyAdmin após executar o criar_tabelas.sql
-- ==================================================

-- Adicionar campo de senha na tabela usuarios (para login)
ALTER TABLE usuarios ADD COLUMN senha VARCHAR(255) AFTER email;

-- Criar tabela de comentários
CREATE TABLE IF NOT EXISTS comentarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    comentario TEXT NOT NULL,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_data (data_comentario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Atualizar usuários existentes com senha padrão (123456)
-- IMPORTANTE: Peça aos usuários para alterarem as senhas depois!
UPDATE usuarios SET senha = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE senha IS NULL;
