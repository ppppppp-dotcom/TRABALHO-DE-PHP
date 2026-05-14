<?php
// Arquivo central de funções do sistema
// Iniciamos a sessão para poder identificar o usuário logado em todas as páginas
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Função para ler dados de um arquivo JSON
 * Ela verifica se o arquivo existe, se não existir cria um vazio, e retorna o conteúdo como um Array do PHP
 */
function buscarDados($file) {
    $path = __DIR__ . "/../data/$file.json";
    // Se o arquivo não existir (ex: primeira vez rodando), cria um arquivo com array vazio
    if (!file_exists($path)) file_put_contents($path, json_encode([]));
    // Pega o texto do arquivo e transforma de volta em um Array PHP para ser usado no código
    return json_decode(file_get_contents($path), true) ?: [];
}

/**
 * Função para salvar dados no arquivo JSON
 * Recebe o nome do arquivo e o array de dados, transformando-o em texto (JSON) formatado
 */
function salvarDados($file, $dados) {
    $path = __DIR__ . "/../data/$file.json";
    // Salva no arquivo com formatação bonitinha (PRETTY_PRINT) e aceitando acentos (UNESCAPED_UNICODE)
    file_put_contents($path, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Registra uma ação no histórico da tarefa (Audit Trail)
 * Útil para saber quem mudou o quê e quando
 */
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

/**
 * Middleware de Segurança: Verifica se o usuário está logado
 * Se não houver ID de usuário na sessão, redireciona para a tela de login
 */
function validarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Função de Sanitização (Segurança)
 * Remove espaços em branco e transforma caracteres especiais de HTML para evitar ataques (XSS)
 */
function filtrar($val) {
    return htmlspecialchars(trim($val));
}

/**
 * Recupera o tema atual (claro ou escuro) salvo no Cookie do navegador
 */
function temaAtual() {
    return $_COOKIE['app_theme'] ?? 'light';
}
?>
