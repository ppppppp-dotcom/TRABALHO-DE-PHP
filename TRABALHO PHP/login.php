<?php
// login.php
require_once 'includes/functions.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];

    $usuarios = getData('usuarios');
    foreach ($usuarios as $u) {
        if ($u['email'] === $email && password_verify($senha, $u['senha'])) {
            $_SESSION['usuario_id'] = $u['id'];
            $_SESSION['usuario_nome'] = $u['nome'];
            $_SESSION['usuario_email'] = $u['email'];
            header('Location: index.php');
            exit;
        }
    }
    $erro = "E-mail ou senha incorretos.";
}

include 'includes/header.php';
?>

<div class="auth-card">
    <h2 style="margin-bottom: 1.5rem; text-align: center;">Acessar Conta</h2>
    
    <?php if ($erro): ?>
        <div class="alert alert-error"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
    </form>
    
    <p style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem;">
        Não tem uma conta? <a href="cadastro.php" style="color: var(--primary); font-weight: 600;">Cadastre-se</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
