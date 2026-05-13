<?php
// detalhes_tarefa.php
require_once 'includes/functions.php';
checkAuth();

$taskId = $_GET['id'] ?? '';
$tarefas = getData('tarefas');
$task = null;
$taskIndex = -1;

foreach ($tarefas as $index => $t) {
    if ($t['id'] === $taskId) {
        $task = $t;
        $taskIndex = $index;
        break;
    }
}

if (!$task) {
    header('Location: index.php');
    exit;
}

// Permissões
$podeAlterarStatus = ($_SESSION['usuario_id'] === $task['criador_id'] || $_SESSION['usuario_id'] === $task['responsavel_id']);
$podeEditarExcluir = ($_SESSION['usuario_id'] === $task['criador_id']);

// Processar Novo Comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $texto = sanitize($_POST['comentario']);
    if (!empty($texto)) {
        $tarefas[$taskIndex]['comentarios'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'texto' => $texto,
            'data_hora' => date('d/m/Y H:i:s')
        ];
        saveData('tarefas', $tarefas);
        header("Location: detalhes_tarefa.php?id=$taskId");
        exit;
    }
}

// Processar Alteração de Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_status']) && $podeAlterarStatus) {
    $novoStatus = $_POST['novo_status'];
    if ($novoStatus !== $task['status']) {
        $statusAntigo = $task['status'];
        $tarefas[$taskIndex]['status'] = $novoStatus;
        
        // Registrar no histórico
        $tarefas[$taskIndex]['historico'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'mensagem' => "Alterou o status de '$statusAntigo' para '$novoStatus'.",
            'data_hora' => date('d/m/Y H:i:s')
        ];
        
        saveData('tarefas', $tarefas);
        header("Location: detalhes_tarefa.php?id=$taskId");
        exit;
    }
}

include 'includes/header.php';
?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Coluna Principal -->
    <div>
        <div style="background: var(--card-bg); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <h1><?php echo $task['titulo']; ?></h1>
                <div style="display: flex; gap: 0.5rem;">
                    <?php if ($podeEditarExcluir): ?>
                        <a href="editar_tarefa.php?id=<?php echo $task['id']; ?>" class="btn btn-outline" style="padding: 0.5rem 1rem;">Editar</a>
                        <a href="excluir_tarefa.php?id=<?php echo $task['id']; ?>" class="btn btn-danger" style="padding: 0.5rem 1rem;" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    <?php endif; ?>
                </div>
            </div>

            <p style="white-space: pre-wrap; color: var(--text-main); margin-bottom: 2rem; line-height: 1.6;">
                <?php echo $task['descricao']; ?>
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.5rem; background: var(--bg-light); border-radius: 8px;">
                <div>
                    <small style="color: var(--text-muted); display: block;">Criado por:</small>
                    <strong><?php echo $task['criador_nome']; ?></strong>
                </div>
                <div>
                    <small style="color: var(--text-muted); display: block;">Responsável:</small>
                    <strong><?php echo $task['responsavel_nome']; ?></strong>
                </div>
                <div>
                    <small style="color: var(--text-muted); display: block;">Prazo Final:</small>
                    <strong><?php echo date('d/m/Y', strtotime($task['data_limite'])); ?></strong>
                </div>
                <div>
                    <small style="color: var(--text-muted); display: block;">Status Atual:</small>
                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                        <?php echo $task['status']; ?>
                    </span>
                </div>
            </div>

            <?php if ($podeAlterarStatus): ?>
                <form method="POST" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Atualizar Status:</label>
                    <div style="display: flex; gap: 1rem;">
                        <select name="novo_status" class="form-control" style="max-width: 250px;">
                            <option value="Pendente" <?php echo $task['status'] === 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="Em andamento" <?php echo $task['status'] === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                            <option value="Concluída" <?php echo $task['status'] === 'Concluída' ? 'selected' : ''; ?>>Concluída</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Comentários -->
        <div class="comments-section" style="background: var(--card-bg); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1.5rem;">Comentários (<?php echo count($task['comentarios']); ?>)</h3>
            
            <form method="POST" style="margin-bottom: 2rem;">
                <textarea name="comentario" class="form-control" rows="3" placeholder="Escreva um comentário..." required></textarea>
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Enviar Comentário</button>
            </form>

            <?php foreach (array_reverse($task['comentarios']) as $comentario): ?>
                <div class="comment">
                    <div class="comment-meta">
                        <?php echo $comentario['usuario']; ?> &bull; <?php echo $comentario['data_hora']; ?>
                    </div>
                    <div style="font-size: 0.9375rem;"><?php echo nl2br($comentario['texto']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Coluna Lateral: Histórico -->
    <div>
        <div style="background: var(--card-bg); padding: 1.5rem; border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem; font-size: 1.125rem;">Histórico de Alterações</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach (array_reverse($task['historico']) as $log): ?>
                    <div style="font-size: 0.8125rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                        <div style="color: var(--text-muted); margin-bottom: 0.25rem;">
                            <strong><?php echo $log['usuario']; ?></strong> em <?php echo $log['data_hora']; ?>
                        </div>
                        <div style="font-weight: 500;"><?php echo $log['mensagem']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
