<?php
// excluir_tarefa.php
require_once 'includes/functions.php';
checkAuth();

$taskId = $_GET['id'] ?? '';
$tarefas = getData('tarefas');
$novaLista = [];
$encontrou = false;

foreach ($tarefas as $t) {
    if ($t['id'] === $taskId) {
        // Apenas o criador pode excluir
        if ($t['criador_id'] === $_SESSION['usuario_id']) {
            $encontrou = true;
            continue; // Pula a adição à nova lista (exclui)
        } else {
            header('Location: index.php?erro=sem_permissao');
            exit;
        }
    }
    $novaLista[] = $t;
}

if ($encontrou) {
    saveData('tarefas', $novaLista);
}

header('Location: index.php');
exit;
?>
