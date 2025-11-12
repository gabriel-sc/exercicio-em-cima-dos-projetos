<?php
session_start();
require_once 'config/database.php';

$mensagem = '';
$tipo_mensagem = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $autor_id = (int)($_POST['autor_id'] ?? 0);
    $status = $_POST['status'] ?? 'rascunho';
    
    if (empty($titulo) || empty($conteudo) || $autor_id <= 0) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            $conn = conectarBanco();
            
            $stmt = $conn->prepare("INSERT INTO posts (titulo, conteudo, autor_id, status, data_publicacao) 
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssis", $titulo, $conteudo, $autor_id, $status);
            
            if ($stmt->execute()) {
                $mensagem = 'Publicação criada com sucesso!';
                $tipo_mensagem = 'sucesso';
                
                // Limpar campos após sucesso
                $_POST = array();
            } else {
                $mensagem = 'Erro ao criar publicação: ' . $stmt->error;
                $tipo_mensagem = 'erro';
            }
            
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $mensagem = 'Erro: ' . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}

// Buscar lista de usuários para selecionar como autor
try {
    $conn = conectarBanco();
    $usuarios = $conn->query("SELECT id, nome FROM usuarios ORDER BY nome");
} catch (Exception $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Publicação - Meu Site</title>
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
                    <li><a href="cadastro.php">Cadastro</a></li>
                    <li><a href="nova-publicacao.php" class="active">Nova Publicação</a></li>
                    <!-- Adicionando link de login/logout -->
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <h2>Criar Nova Publicação</h2>
        
        <?php if ($mensagem): ?>
            <div class="mensagem <?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-cadastro">
            <div class="form-group">
                <label for="titulo">Título *</label>
                <input 
                    type="text" 
                    id="titulo" 
                    name="titulo" 
                    required
                    placeholder="Digite o título da publicação"
                    value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="conteudo">Conteúdo *</label>
                <textarea 
                    id="conteudo" 
                    name="conteudo" 
                    rows="10" 
                    required
                    placeholder="Digite o conteúdo da publicação"
                ><?php echo htmlspecialchars($_POST['conteudo'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="autor_id">Autor *</label>
                <select id="autor_id" name="autor_id" required>
                    <option value="">Selecione um autor</option>
                    <?php while($usuario = $usuarios->fetch_assoc()): ?>
                        <option value="<?php echo $usuario['id']; ?>"
                            <?php echo (isset($_POST['autor_id']) && $_POST['autor_id'] == $usuario['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($usuario['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="rascunho" <?php echo (isset($_POST['status']) && $_POST['status'] == 'rascunho') ? 'selected' : ''; ?>>
                        Rascunho
                    </option>
                    <option value="publicado" <?php echo (isset($_POST['status']) && $_POST['status'] == 'publicado') ? 'selected' : ''; ?>>
                        Publicado
                    </option>
                </select>
            </div>

            <button type="submit" class="btn">Criar Publicação</button>
            <a href="index.php" class="btn-secondary">Cancelar</a>
        </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
