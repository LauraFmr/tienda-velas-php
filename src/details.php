<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/Config.php';
require __DIR__ . '/services/ProductosService.php';
require __DIR__ . '/services/SessionService.php';

use config\Config;
use services\ProductosService;
use services\SessionService;

$config = Config::getInstance();
$srv = new ProductosService($config->db);
$session = SessionService::getInstance();
$id = (int)($_GET['id'] ?? 0);
$p = $srv->findById($id);
if (!$p) {
    header('Location: /index.php');
    exit;
}
include __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Producto #<?= $p->id ?></h1>
<div class="row g-4">
    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <?php if ($p->imagen): ?><img src="<?= htmlspecialchars($p->imagen) ?>" class="img-fluid"><?php else: ?><div class="text-muted">Sin imagen</div><?php endif; ?>
        </div>
    </div>
    <div class="col-md-8">
        <dl class="row">
            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($p->nombre) ?></dd>
            <dt class="col-sm-3">Descripción</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($p->descripcion ?? '-') ?></dd>
            <dt class="col-sm-3">Categoría</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($p->categoriaNombre ?? '-') ?></dd>
            <dt class="col-sm-3">Fragancia</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($p->fraganciaNombre ?? '-') ?></dd>
            <dt class="col-sm-3">Precio</dt>
            <dd class="col-sm-9"><?= number_format($p->precio, 2, ',', '.') ?> €</dd>
            <dt class="col-sm-3">Stock</dt>
            <dd class="col-sm-9"><?= (int)$p->stock ?></dd>
            <dt class="col-sm-3">UUID</dt>
            <dd class="col-sm-9"><code><?= $p->uuid ?></code></dd>
        </dl>
        <a class="btn btn-secondary" href="/index.php">Volver</a>
        <?php if ($session->hasRole('ADMIN')): ?>
            <a class="btn btn-primary" href="/update.php?id=<?= $p->id ?>">Editar</a>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/footer.php';
