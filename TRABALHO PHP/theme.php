<?php
$tema = $_COOKIE['app_theme'] ?? 'light';
$novo = ($tema === 'light') ? 'dark' : 'light';

setcookie('app_theme', $novo, time() + (86400 * 30), "/");
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>
