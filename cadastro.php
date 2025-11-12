<?php
require_once 'config/database.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = conectarBanco();
        verificarTabelas($conn);
        
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone']);
        $senha = $_POST['senha'];
        $confirma_senha = $_POST['confirma_senha'];
        
        // Validações básicas
        if (empty($nome)) {
            throw new Exception('Nome é obrigatório');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        
        if (empty($senha) || strlen($senha) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres');
        }
        
        if ($senha !== $confirma_senha) {
            throw new Exception('As senhas não coincidem');
        }
        
        // Verificar se o email já existe
        $verifica = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$verifica) {
            throw new Exception('Erro ao preparar consulta: ' . $conn->error);
        }
        
        $verifica->bind_param("s", $email);
        $verifica->execute();
        $resultado = $verifica->get_result();
        
        if ($resultado->num_rows > 0) {
            $mensagem = 'Este email já está cadastrado!';
            $tipo_mensagem = 'erro';
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception('Erro ao preparar inserção: ' . $conn->error);
            }
            
            $stmt->bind_param("ssss", $nome, $email, $telefone, $senha_hash);
            
            if ($stmt->execute()) {
                $mensagem = 'Usuário cadastrado com sucesso! Você já pode fazer login.';
                $tipo_mensagem = 'sucesso';
            } else {
                throw new Exception('Erro ao cadastrar: ' . $stmt->error);
            }
            
            $stmt->close();
        }
        
        $verifica->close();
        $conn->close();
        
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Meu Site</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1>Meu Site</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="usuarios.php">Usuários</a></li>
                    <li><a href="cadastro.php" class="active">Cadastro</a></li>
                    <li><a href="nova-publicacao.php">Nova Publicação</a></li>
                    <!-- Adicionando link de login -->
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="form-container">
            <h2>Cadastro de Usuário</h2>
            
            <?php if ($mensagem): ?>
                <div class="mensagem <?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="cadastro.php">
                <div class="form-group">
                    <label for="nome">Nome Completo*</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000">
                </div>
                
                <!-- Adicionando campos de senha -->
                <div class="form-group">
                    <label for="senha">Senha* (mínimo 6 caracteres)</label>
                    <input type="password" id="senha" name="senha" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirma_senha">Confirmar Senha*</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
            
            <!-- Adicionando link para login -->
            <p style="margin-top: 20px; text-align: center;">
                Já tem conta? <a href="login.php">Faça login aqui</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
