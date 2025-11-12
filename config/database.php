
<?php

// Ativar exibição de erros para debug (REMOVA isso em produção final)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados InfinityFree
// IMPORTANTE: Substitua estes valores pelos dados do seu painel InfinityFree

define('DB_SERVER', 'sql213.infinityfree.com'); // Seu servidor MySQL
define('DB_USERNAME', 'if0_40161697'); // Seu usuário MySQL
define('DB_PASSWORD', 'ft0hMaLSyJU'); // Sua senha MySQL
define('DB_NAME', 'if0_40161697_Banco'); // Nome do seu banco de dados

function conectarBanco() {
    try {
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        // Verificar conexão
        if ($conn->connect_error) {
            // Log detalhado do erro
            $erro = "❌ ERRO DE CONEXÃO:<br>";
            $erro .= "Código: " . $conn->connect_errno . "<br>";
            $erro .= "Mensagem: " . $conn->connect_error . "<br><br>";
            $erro .= "📋 Verifique:<br>";
            $erro .= "1. Se você criou o banco de dados no painel do InfinityFree<br>";
            $erro .= "2. Se as credenciais em config/database.php estão corretas<br>";
            $erro .= "3. Se o servidor MySQL está ativo (verifique no painel)<br>";
            
            die($erro);
        }
        
        // Definir charset UTF-8
        if (!$conn->set_charset("utf8mb4")) {
            die("❌ Erro ao definir charset: " . $conn->error);
        }
        
        return $conn;
        
    } catch (Exception $e) {
        die("❌ Erro ao conectar: " . $e->getMessage());
    }
}

function verificarTabelas($conn) {
    $tabelas = ['usuarios', 'posts', 'comentarios'];
    $faltam = [];
    
    foreach ($tabelas as $tabela) {
        $resultado = $conn->query("SHOW TABLES LIKE '$tabela'");
        if ($resultado->num_rows == 0) {
            $faltam[] = $tabela;
        }
    }
    
    if (!empty($faltam)) {
        $erro = "❌ TABELAS NÃO ENCONTRADAS: " . implode(', ', $faltam) . "<br><br>";
        $erro .= "📋 Você precisa:<br>";
        $erro .= "1. Acessar phpMyAdmin no painel do InfinityFree<br>";
        $erro .= "2. Selecionar seu banco de dados<br>";
        $erro .= "3. Clicar na aba 'SQL'<br>";
        $erro .= "4. Copiar e executar todo o conteúdo dos arquivos 'scripts/criar_tabelas.sql' e 'scripts/adicionar_comentarios.sql'<br>";
        die($erro);
    }
    
    return true;
}
?>
