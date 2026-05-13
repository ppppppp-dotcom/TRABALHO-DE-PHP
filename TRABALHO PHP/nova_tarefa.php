<?php
// nova_tarefa.php
require_once 'includes/functions.php';
checkAuth();

$usuarios = getData('usuarios');
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo']);
    $descricao = sanitize($_POST['descricao']);
    $data_limite = $_POST['data_limite'];
    $responsavel_id = $_POST['responsavel_id'];

    if (empty($titulo) || empty($data_limite) || empty($responsavel_id)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Buscar nome do responsável
        $responsavel_nome = "";
        foreach ($usuarios as $u) {
            if ($u['id'] === $responsavel_id) {
                $responsavel_nome = $u['nome'];
                break;
            }
        }

        $tarefas = getData('tarefas');
        $novaTarefa = [
            'id' => uniqid(),
            'titulo' => $titulo,
            'descricao' => $descricao,
            'data_limite' => $data_limite,
            'responsavel_id' => $responsavel_id,
            'responsavel_nome' => $responsavel_nome,
            'criador_id' => $_SESSION['usuario_id'],
            'criador_nome' => $_SESSION['usuario_nome'],
            'status' => 'Pendente',
            'comentarios' => [],
            'historico' => [
                [
                    'usuario' => $_SESSION['usuario_nome'],
                    'mensagem' => 'Tarefa criada.',
                    'data_hora' => date('d/m/Y H:i:s')
                ]
            ]
        ];
        
        $tarefas[] = $novaTarefa;
        saveData('tarefas', $tarefas);
        header('Location: index.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div style="max-width: 600px; margin: 0 auto; background: var(--card-bg); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow);">
    <h2 style="margin-bottom: 2rem;">Criar Nova Tarefa</h2>

    <?php if ($erro): ?>
        <div class="alert alert-error"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Título da Tarefa</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label>Responsável</label>
            <select name="responsavel_id" class="form-control" required>
                <option value="">Selecione um usuário</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo $u['nome']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar Tarefa</button>
            <a href="index.php" class="btn btn-outline" style="flex: 1;">Cancelar</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
