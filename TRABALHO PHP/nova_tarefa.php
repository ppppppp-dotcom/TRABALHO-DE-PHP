<?php
// Página para criar novas tarefas no sistema
require_once 'includes/functions.php';
validarLogin();

// Busca usuários para preencher o campo de "Responsável"
$usuarios = buscarDados('usuarios');
$erro = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = filtrar($_POST['titulo']);
    $descricao = filtrar($_POST['descricao']);
    $data_limite = $_POST['data_limite'];
    $responsavel_id = $_POST['responsavel_id'];

    // Validação básica: campos obrigatórios
    if ($titulo && $data_limite && $responsavel_id) {
        $nome_responsavel = "";
        // Procura o nome do responsável selecionado no array de usuários
        foreach ($usuarios as $usuario) {
            if ($usuario['id'] === $responsavel_id) { $nome_responsavel = $usuario['nome']; break; }
        }

        $lista = buscarDados('tarefas');
        // Adiciona a nova tarefa ao array
        $lista[] = [
            'id' => uniqid(), // Gera um ID único aleatório
            'titulo' => $titulo,
            'descricao' => $descricao,
            'data_limite' => $data_limite,
            'responsavel_id' => $responsavel_id,
            'responsavel_nome' => $nome_responsavel,
            'criador_id' => $_SESSION['usuario_id'],
            'criador_nome' => $_SESSION['usuario_nome'],
            'status' => 'Pendente', // Toda tarefa nova nasce como Pendente
            'comentarios' => [],
            'historico' => [
                ['usuario' => $_SESSION['usuario_nome'], 'mensagem' => 'Tarefa criada.', 'data' => date('d/m/Y H:i')]
            ]
        ];
        
        // Salva a lista atualizada no arquivo JSON
        salvarDados('tarefas', $lista);
        header('Location: index.php');
        exit;
    } else {
        $erro = "Preencha os campos obrigatórios.";
    }
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="max-width: 600px; flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 25px;">Nova Tarefa</h2>

    <?php if ($erro): ?>
        <p style="color: var(--perigo); margin-bottom: 15px;"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>Título</label>
            <input type="text" name="titulo" class="campo-txt" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Descrição</label>
            <textarea name="descricao" class="campo-txt" rows="3" required></textarea>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="campo-txt" required min="<?= date('Y-m-d') ?>">
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Responsável</label>
            <select name="responsavel_id" class="campo-txt" required>
                <option value="">Selecione...</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id'] ?>"><?= $usuario['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-principal" style="flex: 1;">SALVAR</button>
            <a href="index.php" class="btn btn-contorno" style="flex: 1; text-align: center;">CANCELAR</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
