<?php
require_once 'includes/functions.php';
validarLogin();

$id = $_GET['id'] ?? '';
$list = buscarDados('tarefas');
$users = buscarDados('usuarios');
$t = null;
$pos = -1;

foreach ($list as $i => $item) {
    if ($item['id'] === $id) { $t = $item; $pos = $i; break; }
}

if (!$t || $t['criador_id'] !== $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tit = filtrar($_POST['titulo']);
    $desc = filtrar($_POST['descricao']);
    $fim = $_POST['data_limite'];
    $resp = $_POST['responsavel_id'];

    if ($tit && $fim && $resp) {
        $mudou = false;
        if ($tit !== $t['titulo']) $mudou = true;
        if ($desc !== $t['descricao']) $mudou = true;
        if ($fim !== $t['data_limite']) $mudou = true;
        if ($resp !== $t['responsavel_id']) $mudou = true;

        if ($mudou) {
            $list[$pos]['titulo'] = $tit;
            $list[$pos]['descricao'] = $desc;
            $list[$pos]['data_limite'] = $fim;
            $list[$pos]['responsavel_id'] = $resp;
            
            foreach ($users as $u) if ($u['id'] === $resp) $list[$pos]['responsavel_nome'] = $u['nome'];

            $list[$pos]['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => "Editou os dados da tarefa.",
                'data' => date('d/m/Y H:i')
            ];

            salvarDados('tarefas', $list);
        }
        
        header("Location: detalhes_tarefa.php?id=$id");
        exit;
    } else {
        $err = "Preencha tudo.";
    }
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="max-width: 600px; flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 25px;">Editar Tarefa</h2>

    <?php if ($err): ?>
        <p style="color: var(--perigo); margin-bottom: 15px;"><?= $err ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>Título</label>
            <input type="text" name="titulo" class="campo-txt" value="<?= $t['titulo'] ?>" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Descrição</label>
            <textarea name="descricao" class="campo-txt" rows="3" required><?= $t['descricao'] ?></textarea>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="campo-txt" value="<?= $t['data_limite'] ?>" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Responsável</label>
            <select name="responsavel_id" class="campo-txt" required>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $u['id'] === $t['responsavel_id'] ? 'selected' : '' ?>>
                        <?= $u['nome'] ?>
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
