<?php
$hote = $_SERVER['SERVER_NAME'] ?? '';
$est_local = in_array($hote, ['localhost', '127.0.0.1'], true);
$url_site = $est_local ? 'http://localhost/vite-et-gourmand/site-web' : '';
$email_expediteur = $est_local ? 'noreply@vite-et-gourmand.test' : '';

if (is_file(__DIR__ . '/parametres-site.local.php')) {
    require __DIR__ . '/parametres-site.local.php';
}
