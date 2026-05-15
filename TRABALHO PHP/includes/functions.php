<?php
// Arquivo central de funções do sistema
// Iniciamos a sessão para poder identificar o usuário logado em todas as páginas
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Função para ler dados de um arquivo JSON
 * Ela verifica se o arquivo existe, se não existir cria um vazio, e retorna o conteúdo como um Array do PHP
 */
function buscarDados($arquivo) {
    $caminho = __DIR__ . "/../data/$arquivo.json";
    // Se o arquivo não existir (ex: primeira vez rodando), cria um arquivo com array vazio
    if (!file_exists($caminho)) file_put_contents($caminho, json_encode([]));
    // Pega o texto do arquivo e transforma de volta em um Array PHP para ser usado no código
    return json_decode(file_get_contents($caminho), true) ?: [];
}

/**
 * Função para salvar dados no arquivo JSON
 * Recebe o nome do arquivo e o array de dados, transformando-o em texto (JSON) formatado
 */
function salvarDados($arquivo, $dados) {
    $caminho = __DIR__ . "/../data/$arquivo.json";
    // Salva no arquivo com formatação bonitinha (PRETTY_PRINT) e aceitando acentos (UNESCAPED_UNICODE)
    file_put_contents($caminho, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Registra uma ação no histórico da tarefa (Audit Trail)
 * Útil para saber quem mudou o quê e quando
 */
function registrarHistorico($id, $mensagem) {
    $lista = buscarDados('tarefas');
    foreach ($lista as &$tarefa) {
        if ($tarefa['id'] == $id) {
            $tarefa['historico'][] = [
                'usuario' => $_SESSION['usuario_nome'],
                'mensagem' => $mensagem,
                'data' => date('d/m/Y H:i')
            ];
            break;
        }
    }
    salvarDados('tarefas', $lista);
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
function filtrar($valor) {
    return htmlspecialchars(trim($valor));
}

/**
 * Recupera o tema atual (claro ou escuro) salvo no Cookie do navegador
 */
function temaAtual() {
    return $_COOKIE['tema_aplicacao'] ?? 'claro';
}
?>
