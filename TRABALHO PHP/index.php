<?php
// Carrega o arquivo de funções e verifica se o usuário está logado
require_once 'includes/functions.php';
validarLogin();

// Busca a lista de tarefas e a lista de usuários cadastrados nos arquivos JSON
$tarefas = buscarDados('tarefas');
$usuarios = buscarDados('usuarios');

// Lógica de Filtros: Pega o valor enviado via GET ou via Cookie (persistência)
// O operador ?? serve para pegar o primeiro valor que não seja nulo (valor enviado > cookie > vazio)
$status_filtro = $_GET['status'] ?? $_COOKIE['status_filtro'] ?? '';
$responsavel_filtro = $_GET['responsavel'] ?? $_COOKIE['responsavel_filtro'] ?? '';

// Salva a escolha do filtro em um Cookie por 1 hora para o usuário não perder a seleção ao navegar
setcookie('status_filtro', $status_filtro, time() + 3600);
setcookie('responsavel_filtro', $responsavel_filtro, time() + 3600);

// Aplica a filtragem no array de tarefas
$itens = array_filter($tarefas, function($tarefa) use ($status_filtro, $responsavel_filtro) {
    // Verifica se o status bate com o filtro ou se o filtro está vazio (mostrar todos)
    $status_corresponde = $status_filtro === '' || $tarefa['status'] === $status_filtro;
    // Verifica se o responsável bate com o filtro ou se o filtro está vazio
    $responsavel_corresponde = $responsavel_filtro === '' || $tarefa['responsavel_id'] === $responsavel_filtro;
    return $status_corresponde && $responsavel_corresponde;
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
            <option value="Pendente" <?= $status_filtro == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="Em andamento" <?= $status_filtro == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="Concluída" <?= $status_filtro == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
        </select>
    </div>
    
    <div class="campo-grupo">
        <label>Responsável</label>
        <select name="responsavel" class="campo-txt">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>" <?= $responsavel_filtro == $usuario['id'] ? 'selected' : '' ?>>
                    <?= $usuario['nome'] ?>
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
        <?php foreach ($itens as $tarefa): ?>
            <!-- Card Individual da Tarefa -->
            <div class="cartao-tarefa">
                <div class="cartao-topo">
                    <!-- Badge de status dinâmico com base no valor -->
                    <span class="etiqueta etiqueta-<?= strtolower(str_replace(' ', '-', $tarefa['status'])) ?>">
                        <?= $tarefa['status'] ?>
                    </span>
                    <span class="prazo">
                        Prazo: <?= date('d/m/Y', strtotime($tarefa['data_limite'])) ?>
                    </span>
                </div>
                
                <h3><?= $tarefa['titulo'] ?></h3>
                <!-- mb_strimwidth corta o texto se for muito longo para não quebrar o layout -->
                <p class="resumo"><?= mb_strimwidth($tarefa['descricao'], 0, 100, "...") ?></p>
                
                <div class="acoes-cartao">
                    <a href="detalhes_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-contorno">Ver Detalhes</a>
                </div>

                <div class="rodape-cartao">
                    <span>Criador: <b><?= $tarefa['criador_nome'] ?></b></span>
                    <span>Responsável: <b><?= $tarefa['responsavel_nome'] ?></b></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php 
// Inclui o rodapé do site
include 'includes/footer.php'; 
?>
