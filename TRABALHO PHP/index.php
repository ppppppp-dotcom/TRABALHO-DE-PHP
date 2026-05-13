<?php
// index.php
require_once 'includes/functions.php';
checkAuth();

// Nomes e RGMs dos integrantes (conforme requisito 12)
/*
   Integrantes:
   - Pedro Silva (RGM: 1234567-8)
   - Maria Oliveira (RGM: 8765432-1)
*/

$tarefas = getData('tarefas');
$usuarios = getData('usuarios');

// Filtros
$filtro_status = isset($_GET['status']) ? $_GET['status'] : (isset($_COOKIE['last_filter_status']) ? $_COOKIE['last_filter_status'] : '');
$filtro_responsavel = isset($_GET['responsavel']) ? $_GET['responsavel'] : (isset($_COOKIE['last_filter_resp']) ? $_COOKIE['last_filter_resp'] : '');

// Salvar filtros em cookies para persistência (requisito 8)
setcookie('last_filter_status', $filtro_status, time() + 3600);
setcookie('last_filter_resp', $filtro_responsavel, time() + 3600);

$filteredTasks = array_filter($tarefas, function($t) use ($filtro_status, $filtro_responsavel) {
    $matchStatus = $filtro_status === '' || $t['status'] === $filtro_status;
    $matchResp = $filtro_responsavel === '' || $t['responsavel_id'] === $filtro_responsavel;
    return $matchStatus && $matchResp;
});

// Ordenar por data limite
usort($filteredTasks, function($a, $b) {
    return strtotime($a['data_limite']) - strtotime($b['data_limite']);
});

include 'includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Minhas Tarefas</h1>
    <a href="nova_tarefa.php" class="btn btn-primary">+ Nova Tarefa</a>
</div>

<form method="GET" class="filters-bar">
    <div class="form-group" style="margin-bottom: 0;">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="">Todos</option>
            <option value="Pendente" <?php echo $filtro_status === 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
            <option value="Em andamento" <?php echo $filtro_status === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
            <option value="Concluída" <?php echo $filtro_status === 'Concluída' ? 'selected' : ''; ?>>Concluída</option>
        </select>
    </div>
    
    <div class="form-group" style="margin-bottom: 0;">
        <label>Responsável</label>
        <select name="responsavel" class="form-control">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php echo $filtro_responsavel === $u['id'] ? 'selected' : ''; ?>>
                    <?php echo $u['nome']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="btn btn-outline">Filtrar</button>
    <a href="index.php" class="btn btn-outline" style="border-color: transparent;">Limpar</a>
</form>

<div class="task-grid">
    <?php if (empty($filteredTasks)): ?>
        <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 3rem;">Nenhuma tarefa encontrada.</p>
    <?php else: ?>
        <?php foreach ($filteredTasks as $task): ?>
            <div class="task-card">
                <div class="task-header">
                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                        <?php echo $task['status']; ?>
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                        Prazo: <?php echo date('d/m/Y', strtotime($task['data_limite'])); ?>
                    </span>
                </div>
                
                <h3 style="margin-bottom: 0.5rem;"><?php echo $task['titulo']; ?></h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; flex-grow: 1;">
                    <?php echo substr($task['descricao'], 0, 100) . (strlen($task['descricao']) > 100 ? '...' : ''); ?>
                </p>
                
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <a href="detalhes_tarefa.php?id=<?php echo $task['id']; ?>" class="btn btn-outline" style="flex: 1; padding: 0.5rem;">Ver Detalhes</a>
                </div>

                <div class="task-meta">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Por: <strong><?php echo $task['criador_nome']; ?></strong></span>
                        <span>Resp: <strong><?php echo $task['responsavel_nome']; ?></strong></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
