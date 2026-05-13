<?php
// theme.php
$currentTheme = isset($_COOKIE['app_theme']) ? $_COOKIE['app_theme'] : 'light';
$newTheme = ($currentTheme === 'light') ? 'dark' : 'light';

setcookie('app_theme', $newTheme, time() + (86400 * 30), "/"); // Expira em 30 dias
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
