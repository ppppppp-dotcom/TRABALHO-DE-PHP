<?php
// cadastro.php
require_once 'includes/functions.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $usuarios = getData('usuarios');
        
        // Validar e-mail duplicado
        $duplicado = false;
        foreach ($usuarios as $u) {
            if ($u['email'] === $email) {
                $duplicado = true;
                break;
            }
        }

        if ($duplicado) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $novoUsuario = [
                'id' => uniqid(),
                'nome' => $nome,
                'email' => $email,
                'senha' => password_hash($senha, PASSWORD_DEFAULT)
            ];
            $usuarios[] = $novoUsuario;
            saveData('usuarios', $usuarios);
            $sucesso = "Conta criada com sucesso! <a href='login.php'>Faça login agora</a>.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-card">
    <h2 style="margin-bottom: 1.5rem; text-align: center;">Criar Conta</h2>
    
    <?php if ($erro): ?>
        <div class="alert alert-error"><?php echo $erro; ?></div>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Cadastrar</button>
    </form>
    
    <p style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem;">
        Já possui conta? <a href="login.php" style="color: var(--primary); font-weight: 600;">Entrar</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
