<?php
// Página para editar tarefas existentes
require_once 'includes/functions.php';
validarLogin();

$id = $_GET['id'] ?? '';
$lista = buscarDados('tarefas');
$usuarios = buscarDados('usuarios');
$tarefa = null;
$posicao = -1;

// Busca a tarefa específica pelo ID
foreach ($lista as $indice => $item) {
    if ($item['id'] === $id) { $tarefa = $item; $posicao = $indice; break; }
}

// Segurança: Apenas o criador da tarefa pode editá-la
if (!$tarefa || $tarefa['criador_id'] !== $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

$erro = "";

// Processa a edição ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = filtrar($_POST['titulo']);
    $descricao = filtrar($_POST['descricao']);
    $data_limite = $_POST['data_limite'];
    $responsavel_id = $_POST['responsavel_id'];

    if ($titulo && $data_limite && $responsavel_id) {
        // Verifica se houve alguma mudança real
        $mudou = false;
        if ($titulo !== $tarefa['titulo']) $mudou = true;
        if ($descricao !== $tarefa['descricao']) $mudou = true;
        if ($data_limite !== $tarefa['data_limite']) $mudou = true;
        if ($responsavel_id !== $tarefa['responsavel_id']) $mudou = true;

        if ($mudou) {
            // Atualiza os dados no array
            $lista[$posicao]['titulo'] = $titulo;
            $lista[$posicao]['descricao'] = $descricao;
            $lista[$posicao]['data_limite'] = $data_limite;
            $lista[$posicao]['responsavel_id'] = $responsavel_id;
            
            // Atualiza o nome do responsável se ele foi trocado
            foreach ($usuarios as $usuario) if ($usuario['id'] === $responsavel_id) $lista[$posicao]['responsavel_nome'] = $usuario['nome'];

            // Adiciona uma entrada no histórico informando a edição
            $lista[$posicao]['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => "Editou os dados da tarefa.",
                'data' => date('d/m/Y H:i')
            ];

            // Salva as alterações no arquivo JSON
            salvarDados('tarefas', $lista);
        }
        
        // Redireciona de volta para a página de detalhes
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    } else {
        $erro = "Preencha tudo.";
    }
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="max-width: 600px; flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 25px;">Editar Tarefa</h2>

    <?php if ($erro): ?>
        <p style="color: var(--perigo); margin-bottom: 15px;"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>Título</label>
            <input type="text" name="titulo" class="campo-txt" value="<?= $tarefa['titulo'] ?>" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Descrição</label>
            <textarea name="descricao" class="campo-txt" rows="3" required><?= $tarefa['descricao'] ?></textarea>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="campo-txt" value="<?= $tarefa['data_limite'] ?>" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Responsável</label>
            <select name="responsavel_id" class="campo-txt" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id'] ?>" <?= $usuario['id'] === $tarefa['responsavel_id'] ? 'selected' : '' ?>>
                        <?= $usuario['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-principal" style="flex: 1;">ATUALIZAR</button>
            <a href="detalhes_tarefa.php?id=<?= $id ?>" class="btn btn-contorno" style="flex: 1; text-align: center;">VOLTAR</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
