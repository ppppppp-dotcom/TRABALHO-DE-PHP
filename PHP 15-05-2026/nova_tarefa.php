<?php
require_once 'includes/functions.php';
validarLogin();

$users = buscarDados('usuarios');
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tit = filtrar($_POST['titulo']);
    $desc = filtrar($_POST['descricao']);
    $fim = $_POST['data_limite'];
    $resp = $_POST['responsavel_id'];

    if ($tit && $fim && $resp) {
        $nome_r = "";
        foreach ($users as $u) {
            if ($u['id'] === $resp) { $nome_r = $u['nome']; break; }
        }

        $list = buscarDados('tarefas');
        $list[] = [
            'id' => uniqid(),
            'titulo' => $tit,
            'descricao' => $desc,
            'data_limite' => $fim,
            'responsavel_id' => $resp,
            'responsavel_nome' => $nome_r,
            'criador_id' => $_SESSION['usuario_id'],
            'criador_nome' => $_SESSION['usuario_nome'],
            'status' => 'Pendente', 
            'comentarios' => [],
            'historico' => [
                ['usuario' => $_SESSION['usuario_nome'], 'mensagem' => 'Tarefa criada.', 'data' => date('d/m/Y H:i')]
            ]
        ];
        
        salvarDados('tarefas', $list);
        header('Location: index.php');
        exit;
    } else {
        $err = "Preencha os campos obrigatórios.";
    }
}

include 'includes/header.php';
?>

<div class="centralizar-cartao barra-filtros" style="max-width: 600px; flex-direction: column; align-items: stretch;">
    <h2 style="margin-bottom: 25px;">Nova Tarefa</h2>

    <?php if ($err): ?>
        <p style="color: var(--perigo); margin-bottom: 15px;"><?= $err ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo-grupo">
            <label>Título</label>
            <input type="text" name="titulo" class="campo-txt" required>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Descrição</label>
            <textarea name="descricao" class="campo-txt" rows="3" required></textarea>
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Data Limite</label>
            <input type="date" name="data_limite" class="campo-txt" required min="<?= date('Y-m-d') ?>">
        </div>

        <div class="campo-grupo" style="margin-top: 15px;">
            <label>Responsável</label>
            <select name="responsavel_id" class="campo-txt" required>
                <option value="">Selecione...</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-principal" style="flex: 1;">SALVAR</button>
            <a href="index.php" class="btn btn-contorno" style="flex: 1; text-align: center;">CANCELAR</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
