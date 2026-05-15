<?php
require_once 'includes/functions.php';
validarLogin();

$id = $_GET['id'] ?? '';
$tarefas = buscarDados('tarefas');
$lista_atualizada = [];

foreach ($tarefas as $tarefa) {
    if ($tarefa['id'] === $id) {
        if ($tarefa['criador_id'] !== $_SESSION['usuario_id']) {
            header('Location: index.php');
            exit;
        }
        continue;
    }
    $lista_atualizada[] = $tarefa;
}

salvarDados('tarefas', $lista_atualizada);
header('Location: index.php');
exit;
?>
