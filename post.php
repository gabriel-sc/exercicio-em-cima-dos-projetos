<?php
session_start();
require_once 'config/database.php';
$conn = conectarBanco();

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $comentario = trim($_POST['comentario']);
    $usuario_id = $_SESSION['usuario_id'];
    
    if (!empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO comentarios (post_id, usuario_id, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $usuario_id, $comentario);
        
        if ($stmt->execute()) {
            $mensagem = 'Comentário adicionado com sucesso!';
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = 'Erro ao adicionar comentário.';
            $tipo_mensagem = 'erro';
        }
        $stmt->close();
    }
}

// Buscar post específico
$stmt = $conn->prepare("SELECT p.*, u.nome as autor_nome 
                        FROM posts p 
                        LEFT JOIN usuarios u ON p.autor_id = u.id 
                        WHERE p.id = ? AND p.status = 'publicado'");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$resultado = $stmt->get_result();
$post = $resultado->fetch_assoc();

// Atualizar visualizações
if ($post) {
    $update = $conn->prepare("UPDATE posts SET visualizacoes = visualizacoes + 1 WHERE id = ?");
    $update->bind_param("i", $post_id);
    $update->execute();
    $update->close();
}

$stmt->close();

$comentarios = [];
if ($post) {
    $stmt_comentarios = $conn->prepare("SELECT c.*, u.nome as usuario_nome 
                                        FROM comentarios c 
                                        LEFT JOIN usuarios u ON c.usuario_id = u.id 
                                        WHERE c.post_id = ? 
                                        ORDER BY c.data_comentario DESC");
    $stmt_comentarios->bind_param("i", $post_id);
    $stmt_comentarios->execute();
    $resultado_comentarios = $stmt_comentarios->get_result();
    
    while($comentario = $resultado_comentarios->fetch_assoc()) {
        $comentarios[] = $comentario;
    }
    
    $stmt_comentarios->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post['titulo']) : 'Post não encontrado'; ?> - Meu Site</title>
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
                    <li><a href="nova-publicacao.php">Nova Publicação</a></li>
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
        <?php if ($post): ?>
            <article class="post-full">
                <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                <p class="meta">
                    Por <?php echo htmlspecialchars($post['autor_nome']); ?> 
                    em <?php echo date('d/m/Y H:i', strtotime($post['data_publicacao'])); ?>
                    | <?php echo $post['visualizacoes']; ?> visualizações
                </p>
                <div class="conteudo">
                    <?php echo nl2br(htmlspecialchars($post['conteudo'])); ?>
                </div>
                <a href="index.php" class="btn">← Voltar</a>
            </article>

            <!-- Seção de comentários -->
            <section class="comentarios-section">
                <h3>Comentários (<?php echo count($comentarios); ?>)</h3>
                
                <?php if ($mensagem): ?>
                    <div class="mensagem <?php echo $tipo_mensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário para adicionar comentário -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="form-comentario">
                        <h4>Deixe seu comentário</h4>
                        <form method="POST">
                            <div class="form-group">
                                <textarea 
                                    name="comentario" 
                                    rows="4" 
                                    placeholder="Digite seu comentário..." 
                                    required
                                ></textarea>
                            </div>
                            <button type="submit" class="btn">Comentar</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="mensagem erro">
                        <a href="login.php">Faça login</a> para deixar seu comentário.
                    </div>
                <?php endif; ?>
                
                <!-- Lista de comentários -->
                <div class="comentarios-lista">
                    <?php if (count($comentarios) > 0): ?>
                        <?php foreach($comentarios as $comentario): ?>
                            <div class="comentario-item">
                                <div class="comentario-header">
                                    <strong><?php echo htmlspecialchars($comentario['usuario_nome']); ?></strong>
                                    <span class="comentario-data">
                                        <?php echo date('d/m/Y H:i', strtotime($comentario['data_comentario'])); ?>
                                    </span>
                                </div>
                                <div class="comentario-conteudo">
                                    <?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 2rem 0;">
                            Ainda não há comentários. Seja o primeiro a comentar!
                        </p>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <div class="mensagem erro">
                <h2>Post não encontrado</h2>
                <p>O post que você está procurando não existe ou foi removido.</p>
                <a href="index.php" class="btn">Voltar para home</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
