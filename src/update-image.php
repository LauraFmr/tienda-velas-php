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
if (!$p) {
    header('Location:/index.php');
    exit;
}
include __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Actualizar imagen — <?= htmlspecialchars($p->nombre) ?></h1>
<form method="post" action="/update_image_file.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $p->id ?>">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="border rounded p-3 text-center">
                <?php if ($p->imagen): ?><img src="<?= htmlspecialchars($p->imagen) ?>" class="img-fluid"><?php else: ?><div class="text-muted">Sin imagen</div><?php endif; ?>
            </div>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nueva imagen (JPG/PNG)</label>
            <input type="file" name="imagen" class="form-control" accept="image/png, image/jpeg" required>
            <div class="mt-3">
                <a class="btn btn-secondary" href="/details.php?id=<?= $p->id ?>">Cancelar</a>
                <button class="btn btn-warning">Subir</button>
            </div>
        </div>
    </div>
</form>
<?php include __DIR__ . '/footer.php';
