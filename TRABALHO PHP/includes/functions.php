<?php
// includes/functions.php

/**
 * Função para ler dados de um arquivo JSON
 */
function getData($filename) {
    $path = __DIR__ . '/../data/' . $filename . '.json';
    if (!file_exists($path)) {
        file_put_contents($path, json_encode([]));
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

/**
 * Função para salvar dados em um arquivo JSON
 */
function saveData($filename, $data) {
    $path = __DIR__ . '/../data/' . $filename . '.json';
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Registra uma alteração no histórico de uma tarefa
 */
function logHistory($taskId, $message) {
    $tarefas = getData('tarefas');
    foreach ($tarefas as &$tarefa) {
        if ($tarefa['id'] == $taskId) {
            if (!isset($tarefa['historico'])) {
                $tarefa['historico'] = [];
            }
            $tarefa['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => $message,
                'data_hora' => date('d/m/Y H:i:s')
            ];
            break;
        }
    }
    saveData('tarefas', $tarefas);
}

/**
 * Verifica se o usuário está logado
 */
function checkAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Sanitização básica de inputs
 */
function sanitize($data) {
    return htmlspecialchars(trim($data));
}

/**
 * Define ou recupera o tema do usuário via Cookie
 */
function getTheme() {
    return isset($_COOKIE['app_theme']) ? $_COOKIE['app_theme'] : 'light';
}
?>
