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


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:/index.php');
    exit;
}
$id = (int)($_POST['id'] ?? 0);
$p = $pSrv->findById($id);
if (!$p) {
    header('Location:/index.php');
    exit;
}


if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header('Location:/update-image.php?id=' . $id);
    exit;
}
$f = $_FILES['imagen'];
$allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png'];
if (!isset($allowed[$f['type']])) {
    header('Location:/update-image.php?id=' . $id);
    exit;
}
$ext = $allowed[$f['type']];
$destDir = $config->uploadPath;
if (!is_dir($destDir)) @mkdir($destDir, 0777, true);
$filename = $p->uuid . $ext;
$dest = $destDir . $filename;
move_uploaded_file($f['tmp_name'], $dest);
$url = $config->uploadUrl . $filename;
$pSrv->updateImage($id, $url);
header('Location:/update-image.php?id=' . $id);
