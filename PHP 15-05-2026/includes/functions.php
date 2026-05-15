<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function buscarDados($file) {
    $path = __DIR__ . "/../data/$file.json";
    if (!file_exists($path)) file_put_contents($path, json_encode([]));
    return json_decode(file_get_contents($path), true) ?: [];
}

function salvarDados($file, $dados) {
    $path = __DIR__ . "/../data/$file.json";
    file_put_contents($path, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function registrarHistorico($id, $msg) {
    $list = buscarDados('tarefas');
    foreach ($list as &$t) {
        if ($t['id'] == $id) {
            $t['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => $msg,
                'data' => date('d/m/Y H:i')
            ];
            break;
        }
    }
    salvarDados('tarefas', $list);
}

function validarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

function filtrar($val) {
    return htmlspecialchars(trim($val));
}


function temaAtual() {
    return $_COOKIE['app_theme'] ?? 'light';
}
?>
