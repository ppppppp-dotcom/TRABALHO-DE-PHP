<?php
require_once 'includes/functions.php';
validarLogin();

$id = $_GET['id'] ?? '';
$tarefas = buscarDados('tarefas');
$nova = [];

foreach ($tarefas as $t) {
    if ($t['id'] === $id) {
        if ($t['criador_id'] !== $_SESSION['usuario_id']) {
            header('Location: index.php');
            exit;
        }
        continue;
    }
    $nova[] = $t;
}

salvarDados('tarefas', $nova);
header('Location: index.php');
exit;
?>
