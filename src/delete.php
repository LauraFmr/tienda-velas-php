<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/Config.php';
require __DIR__ . '/services/SessionService.php';
require __DIR__ . '/services/ProductosService.php';

use config\Config;
use services\SessionService;
use services\ProductosService;

$config = Config::getInstance();
$session = SessionService::getInstance();
if (!$session->hasRole('ADMIN')) {
    header('Location:/index.php');
    exit;
}
$pSrv = new ProductosService($config->db);
$id = (int)($_GET['id'] ?? 0);
$p = $pSrv->findById($id);
if ($p) {
    // eliminar archivo si existe
    if ($p->imagen && str_starts_with($p->imagen, $config->uploadUrl)) {
        $file = $config->uploadPath . basename($p->imagen);
        if (is_file($file)) @unlink($file);
    }
    $pSrv->deleteById($id);
}
header('Location:/index.php'); // refresca la lista de productos
