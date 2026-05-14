<?php
// Carrega o arquivo de funções e verifica se o usuário está logado
require_once 'includes/functions.php';
validarLogin();

// Busca a lista de tarefas e a lista de usuários cadastrados nos arquivos JSON
$tasks = buscarDados('tarefas');
$users = buscarDados('usuarios');

// Lógica de Filtros: Pega o valor enviado via GET ou via Cookie (persistência)
// O operador ?? serve para pegar o primeiro valor que não seja nulo (valor enviado > cookie > vazio)
$st = $_GET['status'] ?? $_COOKIE['st'] ?? '';
$re = $_GET['responsavel'] ?? $_COOKIE['re'] ?? '';

// Salva a escolha do filtro em um Cookie por 1 hora para o usuário não perder a seleção ao navegar
setcookie('st', $st, time() + 3600);
setcookie('re', $re, time() + 3600);

// Aplica a filtragem no array de tarefas
$itens = array_filter($tasks, function($t) use ($st, $re) {
    // Verifica se o status bate com o filtro ou se o filtro está vazio (mostrar todos)
    $ok1 = $st === '' || $t['status'] === $st;
    // Verifica se o responsável bate com o filtro ou se o filtro está vazio
    $ok2 = $re === '' || $t['responsavel_id'] === $re;
    return $ok1 && $ok2;
});

// Ordena a lista de tarefas pela data limite (da mais próxima para a mais distante)
// O operador <=> (spaceship) compara dois valores de forma simplificada
usort($itens, function($a, $b) {
    return strtotime($a['data_limite']) <=> strtotime($b['data_limite']);
});

// Inclui o cabeçalho do site
include 'includes/header.php';
?>

<!-- Cabeçalho da página com botão de nova tarefa -->
<div class="topo-pagina">
    <h1>Minhas Tarefas</h1>
    <a href="nova_tarefa.php" class="btn btn-principal">+ Nova Tarefa</a>
</div>

<!-- Formulário de Filtros (Envia via GET para que a URL possa ser compartilhada) -->
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

<!-- Exibição das tarefas em formato de grade (cards) -->
<div class="grade-tarefas">
    <?php if (empty($itens)): ?>
        <p class="aviso-vazio">Nenhuma tarefa encontrada.</p>
    <?php else: ?>
        <?php foreach ($itens as $t): ?>
            <!-- Card Individual da Tarefa -->
            <div class="cartao-tarefa">
                <div class="cartao-topo">
                    <!-- Badge de status dinâmico com base no valor -->
                    <span class="etiqueta etiqueta-<?= strtolower(str_replace(' ', '-', $t['status'])) ?>">
                        <?= $t['status'] ?>
                    </span>
                    <span class="prazo">
                        Prazo: <?= date('d/m/Y', strtotime($t['data_limite'])) ?>
                    </span>
                </div>
                
                <h3><?= $t['titulo'] ?></h3>
                <!-- mb_strimwidth corta o texto se for muito longo para não quebrar o layout -->
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
// Inclui o rodapé do site
include 'includes/footer.php'; 
?>
