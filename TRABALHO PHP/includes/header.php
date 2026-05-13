<?php
// includes/header.php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$theme = getTheme();
?>
<!DOCTYPE html>
<html lang="pt-br" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Tarefas Colaborativo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a href="index.php" class="logo">TaskColab</a>
        <nav class="nav-links">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="index.php">Dashboard</a>
                <a href="nova_tarefa.php">Nova Tarefa</a>
                <span>Olá, <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
                <a href="theme.php" title="Trocar Tema">🌓</a>
                <a href="logout.php" class="btn btn-outline" style="padding: 0.5rem 1rem;">Sair</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="cadastro.php" class="btn btn-primary">Cadastrar</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
