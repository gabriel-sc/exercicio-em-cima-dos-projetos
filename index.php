<?php
require_once 'config/database.php';
$conn = conectarBanco();

// Buscar posts publicados
$sql = "SELECT p.*, u.nome as autor_nome 
        FROM posts p 
        LEFT JOIN usuarios u ON p.autor_id = u.id 
        WHERE p.status = 'publicado' 
        ORDER BY p.data_publicacao DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Site - InfinityFree</title>
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
        <section class="hero">
            <h2>Bem-vindo ao nosso site!</h2>
            <p>Sistema completo com PHP e MySQL hospedado no InfinityFree</p>
        </section>

        <section class="posts">
            <h3>Últimas Publicações</h3>
            
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($post = $resultado->fetch_assoc()): ?>
                    <article class="post-card">
                        <h4><?php echo htmlspecialchars($post['titulo']); ?></h4>
                        <p class="meta">
                            Por <?php echo htmlspecialchars($post['autor_nome']); ?> 
                            em <?php echo date('d/m/Y H:i', strtotime($post['data_publicacao'])); ?>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars(substr($post['conteudo'], 0, 200))); ?>...</p>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn">Ler mais</a>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum post encontrado.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
