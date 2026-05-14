<?php
require_once 'functions.php';
$tema = temaAtual();
?>
<!DOCTYPE html>
<html lang="pt-br" data-theme="<?= $tema ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Tarefas - Pedro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">SISTEMA PHP</a>
        <nav class="nav-links">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="index.php">Início</a>
                <a href="nova_tarefa.php">Nova Tarefa</a>
                <a href="theme.php" title="Mudar Tema">🌓</a>
                <a href="logout.php" class="btn btn-contorno" style="padding: 5px 15px;">Sair</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="cadastro.php" class="btn btn-principal">Criar Conta</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
