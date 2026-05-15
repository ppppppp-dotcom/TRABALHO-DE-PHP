<?php
// Página de Cadastro de novos usuários
require_once 'includes/functions.php';

// Se já estiver logado, não precisa cadastrar
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = "";
$sucesso = "";

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filtrar($_POST['nome']);
    $email = filtrar($_POST['email']);
    $senha = $_POST['senha'];

    // Valida se todos os campos foram preenchidos
    if ($nome && $email && $senha) {
        $usuarios = buscarDados('usuarios');
        
        $existe = false;
        // Verifica se o e-mail já existe no sistema
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email) { $existe = true; break; }
        }

        if ($existe) {
            $erro = "E-mail já cadastrado.";
        } else {
            // Cria o novo usuário com senha criptografada (Hash)
            $usuarios[] = [
                'id' => uniqid(),
                'nome' => $nome,
                'email' => $email,
                'senha' => password_hash($senha, PASSWORD_DEFAULT)
            ];
            salvarDados('usuarios', $usuarios);
            $sucesso = "Conta criada! Já pode entrar.";
        }
    } else {
        $erro = "Preencha tudo.";
    }
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 20px; text-align: center;">Criar Conta</h2>
    
    <?php if ($erro): ?>
        <p style="color: var(--perigo); margin-bottom: 15px; text-align: center;"><?= $erro ?></p>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
        <p style="color: var(--sucesso); margin-bottom: 15px; text-align: center;"><?= $sucesso ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>Nome Completo</label>
            <input type="text" name="nome" class="campo-txt" placeholder="Ex: Pedro Silva" required>
        </div>
        <div class="campo-grupo" style="margin-top: 15px;">
            <label>E-mail</label>
            <input type="email" name="email" class="campo-txt" placeholder="seu@email.com" required>
        </div>
        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Senha</label>
            <input type="password" name="senha" class="campo-txt" placeholder="Mínimo 6 caracteres" required minlength="6">
        </div>
        <button type="submit" class="btn btn-principal" style="width: 100%; margin-top: 25px;">CADASTRAR</button>
    </form>
    
    <p style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
        Já tem conta? <a href="login.php" style="color: var(--destaque);">Faça login</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
