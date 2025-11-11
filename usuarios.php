<?php
require_once 'config/database.php';
$conn = conectarBanco();

// Buscar todos os usuários ativos
$sql = "SELECT * FROM usuarios WHERE ativo = 1 ORDER BY data_cadastro DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Meu Site</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1>Meu Site</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="usuarios.php" class="active">Usuários</a></li>
                    <li><a href="cadastro.php">Cadastro</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <h2>Usuários Cadastrados</h2>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Data de Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while($usuario = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefone']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($usuario['data_cadastro'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum usuário encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Meu Site. Hospedado no InfinityFree.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
