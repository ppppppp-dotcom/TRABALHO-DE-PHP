<?php
// editar_tarefa.php
require_once 'includes/functions.php';
checkAuth();

$taskId = $_GET['id'] ?? '';
$tarefas = getData('tarefas');
$usuarios = getData('usuarios');
$task = null;
$taskIndex = -1;

foreach ($tarefas as $index => $t) {
    if ($t['id'] === $taskId) {
        $task = $t;
        $taskIndex = $index;
        break;
    }
}

// Apenas o criador pode editar
if (!$task || $task['criador_id'] !== $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo']);
    $descricao = sanitize($_POST['descricao']);
    $data_limite = $_POST['data_limite'];
    $responsavel_id = $_POST['responsavel_id'];

    if (empty($titulo) || empty($data_limite) || empty($responsavel_id)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Detectar mudanças para o histórico
        $mudancas = [];
        if ($titulo !== $task['titulo']) $mudancas[] = "alterou o título";
        if ($descricao !== $task['descricao']) $mudancas[] = "editou a descrição";
        if ($data_limite !== $task['data_limite']) $mudancas[] = "alterou o prazo para " . date('d/m/Y', strtotime($data_limite));
        if ($responsavel_id !== $task['responsavel_id']) {
            $novo_resp_nome = "";
            foreach ($usuarios as $u) if ($u['id'] === $responsavel_id) $novo_resp_nome = $u['nome'];
            $mudancas[] = "alterou o responsável para $novo_resp_nome";
            $tarefas[$taskIndex]['responsavel_nome'] = $novo_resp_nome;
        }

        if (!empty($mudancas)) {
            $tarefas[$taskIndex]['titulo'] = $titulo;
            $tarefas[$taskIndex]['descricao'] = $descricao;
            $tarefas[$taskIndex]['data_limite'] = $data_limite;
            $tarefas[$taskIndex]['responsavel_id'] = $responsavel_id;

            $tarefas[$taskIndex]['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => "Editou a tarefa: " . implode(', ', $mudancas) . ".",
                'data_hora' => date('d/m/Y H:i:s')
            ];

            saveData('tarefas', $tarefas);
        }
        
        header("Location: detalhes_tarefa.php?id=$taskId");
        exit;
    }
}

include 'includes/header.php';
?>

<div style="max-width: 600px; margin: 0 auto; background: var(--card-bg); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow);">
    <h2 style="margin-bottom: 2rem;">Editar Tarefa</h2>

    <?php if ($erro): ?>
        <div class="alert alert-error"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Título da Tarefa</label>
            <input type="text" name="titulo" class="form-control" value="<?php echo $task['titulo']; ?>" required>
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" rows="4" required><?php echo $task['descricao']; ?></textarea>
        </div>

        <div class="form-group">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="form-control" value="<?php echo $task['data_limite']; ?>" required>
        </div>

        <div class="form-group">
            <label>Responsável</label>
            <select name="responsavel_id" class="form-control" required>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?php echo $u['id']; ?>" <?php echo $u['id'] === $task['responsavel_id'] ? 'selected' : ''; ?>>
                        <?php echo $u['nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar Alterações</button>
            <a href="detalhes_tarefa.php?id=<?php echo $taskId; ?>" class="btn btn-outline" style="flex: 1;">Cancelar</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
