<?php
// Configurações do banco de dados InfinityFree
// IMPORTANTE: Substitua estes valores pelos dados do seu painel InfinityFree

define('DB_SERVER', 'sql213.infinityfree.com'); // Seu servidor MySQL
define('DB_USERNAME', 'if0_40161697'); // Seu usuário MySQL
define('DB_PASSWORD', 'ft0hMaLSyJU'); // Sua senha MySQL
define('DB_NAME', 'if0_40161697_Banco'); // Nome do seu banco de dados

// Criar conexão
function conectarBanco() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Verificar conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    // Definir charset UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}
?>
