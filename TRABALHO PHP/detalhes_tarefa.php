<?php
// Página de Detalhes e Gestão de uma Tarefa específica
require_once 'includes/functions.php';
validarLogin();

// Pega o ID da URL e busca no arquivo de tarefas
$id = $_GET['id'] ?? '';
$lista = buscarDados('tarefas');
$tarefa = null;
$posicao = -1;

// Loop para encontrar a tarefa no array pelo ID
foreach ($lista as $indice => $item) {
    if ($item['id'] === $id) {
        $tarefa = $item;
        $posicao = $indice;
        break;
    }
}

// Se a tarefa não existir, volta para a lista principal
if (!$tarefa) {
    header('Location: index.php');
    exit;
}

// Regras de Negócio/Permissões
$meu_id = $_SESSION['usuario_id'];
// Responsável e Criador podem mudar o status
$pode_mudar_status = ($meu_id === $tarefa['criador_id'] || $meu_id === $tarefa['responsavel_id']);
// Apenas o criador pode editar ou excluir os dados básicos
$pode_editar = ($meu_id === $tarefa['criador_id']);

// Lógica para adicionar novo Comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $texto = filtrar($_POST['comentario']);
    if ($texto) {
        $lista[$posicao]['comentarios'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'texto' => $texto,
            'data' => date('d/m/Y H:i')
        ];
        salvarDados('tarefas', $lista);
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    }
}

// Lógica para alterar o Status da tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && $pode_mudar_status) {
    $novo = $_POST['status'];
    if ($novo !== $tarefa['status']) {
        $antigo = $tarefa['status'];
        $lista[$posicao]['status'] = $novo;
        // Salva a alteração no histórico para auditoria
        $lista[$posicao]['historico'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'mensagem' => "Mudou o status de '$antigo' para '$novo'",
            'data' => date('d/m/Y H:i')
        ];
        salvarDados('tarefas', $lista);
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    }
}

include 'includes/header.php';
?>

<div class="grade-tarefas" style="grid-template-columns: 2fr 1fr;">
    <!-- Lado Esquerdo: Detalhes da Tarefa e Comentários -->
    <div>
        <div class="cartao-tarefa" style="padding: 30px;">
            <div class="topo-pagina">
                <h1><?= $tarefa['titulo'] ?></h1>
                <div>
                    <?php if ($pode_editar): ?>
                        <a href="editar_tarefa.php?id=<?= $id ?>" class="btn btn-contorno">Editar</a>
                        <a href="excluir_tarefa.php?id=<?= $id ?>" class="btn btn-principal"
                            style="background: var(--perigo);"
                            onclick="return confirm('Excluir esta tarefa definitivamente?')">Excluir</a>
                    <?php endif; ?>
                </div>
            </div>

            <p style="margin-bottom: 25px; white-space: pre-wrap;"><?= nl2br($tarefa['descricao']) ?></p>

            <!-- Grid de Informações Básicas -->
            <div class="barra-filtros"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: var(--fundo);">
                <span>Criador: <b><?= $tarefa['criador_nome'] ?></b></span>
                <span>Responsável: <b><?= $tarefa['responsavel_nome'] ?></b></span>
                <span>Prazo Final: <b><?= date('d/m/Y', strtotime($tarefa['data_limite'])) ?></b></span>
                <span>Status: <b
                        class="etiqueta etiqueta-<?= strtolower(str_replace(' ', '-', $tarefa['status'])) ?>"><?= $tarefa['status'] ?></b></span>
            </div>

            <?php if ($pode_mudar_status): ?>
                <!-- Formulário para troca rápida de status -->
                <form method="POST" style="margin-top: 30px; border-top: 1px solid var(--borda); padding-top: 20px;">
                    <label>Atualizar Status:</label>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select name="status" class="campo-txt" style="width: 200px;">
                            <option value="Pendente" <?= $tarefa['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Em andamento" <?= $tarefa['status'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento
                            </option>
                            <option value="Concluída" <?= $tarefa['status'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                        </select>
                        <button type="submit" class="btn btn-principal">SALVAR</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Seção de Comentários (Interação entre usuários) -->
        <div class="cartao-tarefa" style="margin-top: 25px; padding: 30px;">
            <h3>Comentários (<?= count($tarefa['comentarios']) ?>)</h3>
            <form method="POST" style="margin-top: 20px;">
                <textarea name="comentario" class="campo-txt" placeholder="Escreva uma atualização ou dúvida..."
                    required></textarea>
                <button type="submit" class="btn btn-principal" style="margin-top: 10px;">ENVIAR COMENTÁRIO</button>
            </form>

            <div style="margin-top: 30px;">
                <?php foreach (array_reverse($tarefa['comentarios']) as $comentario): ?>
                    <div
                        style="margin-bottom: 15px; padding: 15px; background: var(--fundo); border-radius: 4px; border-left: 4px solid var(--destaque);">
                        <small style="color: var(--texto-suave);"><b><?= $comentario['usuario'] ?></b> • <?= $comentario['data'] ?></small>
                        <p style="font-size: 0.95rem; margin-top: 8px;"><?= nl2br($comentario['texto']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Histórico de Alterações -->
    <div>
        <div class="cartao-tarefa" style="padding: 20px;">
            <h3 style="font-size: 1.1rem;">Histórico de Ações</h3>
            <div style="margin-top: 15px;">
                <?php foreach (array_reverse($tarefa['historico']) as $registro): ?>
                    <div
                        style="font-size: 0.8rem; margin-bottom: 12px; border-bottom: 1px solid var(--borda); padding-bottom: 8px;">
                        <span style="color: var(--destaque); font-weight: 700;"><?= $registro['usuario'] ?></span><br>
                        <span style="color: var(--texto-suave);"><?= $registro['data'] ?></span><br>
                        <p style="margin-top: 4px;"><?= $registro['mensagem'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>