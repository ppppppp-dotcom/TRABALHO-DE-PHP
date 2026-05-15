<?php
require_once 'includes/functions.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = filtrar($_POST['email']);
    $pass = $_POST['senha'];

    $users = buscarDados('usuarios');
    foreach ($users as $u) {
        if ($u['email'] === $mail && password_verify($pass, $u['senha'])) {
            $_SESSION['usuario_id'] = $u['id'];
            $_SESSION['usuario_nome'] = $u['nome'];
            header('Location: index.php');
            exit;
        }
    }
    $err = "E-mail ou senha incorretos.";
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 20px; text-align: center;">Acessar</h2>
    
    <?php if ($err): ?>
        <p style="color: var(--perigo); margin-bottom: 15px; text-align: center;"><?= $err ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>E-mail</label>
            <input type="email" name="email" class="campo-txt" placeholder="seu@email.com" required>
        </div>
        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Senha</label>
            <input type="password" name="senha" class="campo-txt" placeholder="******" required>
        </div>
        <button type="submit" class="btn btn-principal" style="width: 100%; margin-top: 25px;">ENTRAR</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
        Novo por aqui? <a href="cadastro.php" style="color: var(--destaque);">Crie sua conta</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
