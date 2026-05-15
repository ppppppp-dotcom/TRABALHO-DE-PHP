<?php
$tema = $_COOKIE['tema_aplicacao'] ?? 'claro';
$novo = ($tema === 'claro') ? 'escuro' : 'claro';

setcookie('tema_aplicacao', $novo, time() + (86400 * 30), "/");
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>
