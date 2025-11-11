<?php
require_once 'config/database.php';
$conn = conectarBanco();

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
