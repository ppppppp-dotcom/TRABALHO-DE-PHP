<?php
require_once 'includes/functions.php';
validarLogin();

$id = $_GET['id'] ?? '';
$list = buscarDados('tarefas');
$t = null;
$pos = -1;

foreach ($list as $i => $item) {
    if ($item['id'] === $id) {
        $t = $item;
        $pos = $i;
        break;
    }
}

if (!$t) {
    header('Location: index.php');
    exit;
}

$my_id = $_SESSION['usuario_id'];
$can_status = ($my_id === $t['criador_id'] || $my_id === $t['responsavel_id']);
$can_edit = ($my_id === $t['criador_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $txt = filtrar($_POST['comentario']);
    if ($txt) {
        $list[$pos]['comentarios'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'texto' => $txt,
            'data' => date('d/m/Y H:i')
        ];
        salvarDados('tarefas', $list);
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && $can_status) {
    $novo = $_POST['status'];
    if ($novo !== $t['status']) {
        $old = $t['status'];
        $list[$pos]['status'] = $novo;
        $list[$pos]['historico'][] = [
            'usuario' => $_SESSION['usuario_nome'],
            'mensagem' => "Mudou o status de '$old' para '$novo'",
            'data' => date('d/m/Y H:i')
        ];
        salvarDados('tarefas', $list);
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    }
}

include 'includes/header.php';
?>

<div class="grade-tarefas" style="grid-template-columns: 2fr 1fr;">
    <div>
        <div class="cartao-tarefa" style="padding: 30px;">
            <div class="topo-pagina">
                <h1><?= $t['titulo'] ?></h1>
                <div>
                    <?php if ($can_edit): ?>
                        <a href="editar_tarefa.php?id=<?= $id ?>" class="btn btn-contorno">Editar</a>
                        <a href="excluir_tarefa.php?id=<?= $id ?>" class="btn btn-principal"
                            style="background: var(--perigo);"
                            onclick="return confirm('Excluir esta tarefa definitivamente?')">Excluir</a>
                    <?php endif; ?>
                </div>
            </div>

            <p style="margin-bottom: 25px; white-space: pre-wrap;"><?= nl2br($t['descricao']) ?></p>

            <div class="barra-filtros"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: var(--fundo);">
                <span>Criador: <b><?= $t['criador_nome'] ?></b></span>
                <span>Responsável: <b><?= $t['responsavel_nome'] ?></b></span>
                <span>Prazo Final: <b><?= date('d/m/Y', strtotime($t['data_limite'])) ?></b></span>
                <span>Status: <b
                        class="etiqueta etiqueta-<?= strtolower(str_replace(' ', '-', $t['status'])) ?>"><?= $t['status'] ?></b></span>
            </div>

            <?php if ($can_status): ?>
                <form method="POST" style="margin-top: 30px; border-top: 1px solid var(--borda); padding-top: 20px;">
                    <label>Atualizar Status:</label>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <select name="status" class="campo-txt" style="width: 200px;">
                            <option value="Pendente" <?= $t['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Em andamento" <?= $t['status'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento
                            </option>
                            <option value="Concluída" <?= $t['status'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                        </select>
                        <button type="submit" class="btn btn-principal">SALVAR</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="cartao-tarefa" style="margin-top: 25px; padding: 30px;">
            <h3>Comentários (<?= count($t['comentarios']) ?>)</h3>
            <form method="POST" style="margin-top: 20px;">
                <textarea name="comentario" class="campo-txt" placeholder="Escreva uma atualização ou dúvida..."
                    required></textarea>
                <button type="submit" class="btn btn-principal" style="margin-top: 10px;">ENVIAR COMENTÁRIO</button>
            </form>

            <div style="margin-top: 30px;">
                <?php foreach (array_reverse($t['comentarios']) as $c): ?>
                    <div
                        style="margin-bottom: 15px; padding: 15px; background: var(--fundo); border-radius: 4px; border-left: 4px solid var(--destaque);">
                        <small style="color: var(--texto-suave);"><b><?= $c['usuario'] ?></b> • <?= $c['data'] ?></small>
                        <p style="font-size: 0.95rem; margin-top: 8px;"><?= nl2br($c['texto']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="cartao-tarefa" style="padding: 20px;">
            <h3 style="font-size: 1.1rem;">Histórico de Ações</h3>
            <div style="margin-top: 15px;">
                <?php foreach (array_reverse($t['historico']) as $h): ?>
                    <div
                        style="font-size: 0.8rem; margin-bottom: 12px; border-bottom: 1px solid var(--borda); padding-bottom: 8px;">
                        <span style="color: var(--destaque); font-weight: 700;"><?= $h['usuario'] ?></span><br>
                        <span style="color: var(--texto-suave);"><?= $h['data'] ?></span><br>
                        <p style="margin-top: 4px;"><?= $h['mensagem'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>