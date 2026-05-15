<?php
require_once 'includes/functions.php';
validarLogin();

$tasks = buscarDados('tarefas');
$users = buscarDados('usuarios');

$st = $_GET['status'] ?? $_COOKIE['st'] ?? '';
$re = $_GET['responsavel'] ?? $_COOKIE['re'] ?? '';

setcookie('st', $st, time() + 3600);
setcookie('re', $re, time() + 3600);

$itens = array_filter($tasks, function($t) use ($st, $re) {
    $ok1 = $st === '' || $t['status'] === $st;
    $ok2 = $re === '' || $t['responsavel_id'] === $re;
    return $ok1 && $ok2;
});

usort($itens, function($a, $b) {
    return strtotime($a['data_limite']) <=> strtotime($b['data_limite']);
});

include 'includes/header.php';
?>

<div class="topo-pagina">
    <h1>Minhas Tarefas</h1>
    <a href="nova_tarefa.php" class="btn btn-principal">+ Nova Tarefa</a>
</div>

<form method="GET" class="barra-filtros">
    <div class="campo-grupo">
        <label>Status</label>
        <select name="status" class="campo-txt">
            <option value="">Todos</option>
            <option value="Pendente" <?= $st == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="Em andamento" <?= $st == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="Concluída" <?= $st == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
        </select>
    </div>
    
    <div class="campo-grupo">
        <label>Responsável</label>
        <select name="responsavel" class="campo-txt">
            <option value="">Todos</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $re == $u['id'] ? 'selected' : '' ?>>
                    <?= $u['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="btn btn-contorno">Filtrar</button>
    <a href="index.php" class="btn btn-limpar">Limpar</a>
</form>

<div class="grade-tarefas">
    <?php if (empty($itens)): ?>
        <p class="aviso-vazio">Nenhuma tarefa encontrada.</p>
    <?php else: ?>
        <?php foreach ($itens as $t): ?>
            <div class="cartao-tarefa">
                <div class="cartao-topo">
                    <span class="etiqueta etiqueta-<?= strtolower(str_replace(' ', '-', $t['status'])) ?>">
                        <?= $t['status'] ?>
                    </span>
                    <span class="prazo">
                        Prazo: <?= date('d/m/Y', strtotime($t['data_limite'])) ?>
                    </span>
                </div>
                
                <h3><?= $t['titulo'] ?></h3>
                <p class="resumo"><?= mb_strimwidth($t['descricao'], 0, 100, "...") ?></p>
                
                <div class="acoes-cartao">
                    <a href="detalhes_tarefa.php?id=<?= $t['id'] ?>" class="btn btn-contorno">Ver Detalhes</a>
                </div>

                <div class="rodape-cartao">
                    <span>Criador: <b><?= $t['criador_nome'] ?></b></span>
                    <span>Resp: <b><?= $t['responsavel_nome'] ?></b></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php 
include 'includes/footer.php'; 
?>
