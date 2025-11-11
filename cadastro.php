<?php
require_once 'config/database.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = conectarBanco();
    
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
    
    // Verificar se o email já existe
    $verifica = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $resultado = $verifica->get_result();
    
    if ($resultado->num_rows > 0) {
        $mensagem = 'Este email já está cadastrado!';
        $tipo_mensagem = 'erro';
    } else {
        // Inserir novo usuário
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $telefone);
        
        if ($stmt->execute()) {
            $mensagem = 'Usuário cadastrado com sucesso!';
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = 'Erro ao cadastrar usuário: ' . $conn->error;
            $tipo_mensagem = 'erro';
        }
        
        $stmt->close();
    }
    
    $verifica->close();
    $conn->close();
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
                
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
