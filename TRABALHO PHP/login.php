<?php
// Carrega as funções globais do sistema
require_once 'includes/functions.php';

// Se o usuário já estiver logado, redireciona direto para a página inicial
// pois não faz sentido uma pessoa logada tentar acessar a tela de login
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = "";

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtra o e-mail para prevenir injeção de scripts e outros problemas de segurança
    $email = filtrar($_POST['email']);
    $senha = $_POST['senha'];

    // Busca todos os usuários armazenados no arquivo JSON
    $usuarios = buscarDados('usuarios');
    foreach ($usuarios as $usuario) {
        // Verifica se o e-mail corresponde e se a senha digitada é compatível com o hash salvo
        if ($usuario['email'] === $email && password_verify($senha, $usuario['senha'])) {
            // Se as credenciais estiverem corretas, iniciamos a sessão do usuário
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit;
        }
    }
    // Caso o loop termine sem encontrar o usuário ou a senha falhe, definimos a mensagem de erro
    $erro = "E-mail ou senha incorretos.";
}

// Inclui o cabeçalho da página
include 'includes/header.php';
?>

<!-- Container centralizado para o formulário de login -->
<div class="centralizar-cartao barra-filtros" style="flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 20px; text-align: center;">Acessar</h2>
    
    <?php if ($erro): ?>
        <p style="color: var(--perigo); margin-bottom: 15px; text-align: center;"><?= $erro ?></p>
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
