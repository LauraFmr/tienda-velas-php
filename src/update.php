<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/Config.php';
require __DIR__ . '/services/SessionService.php';
require __DIR__ . '/services/ProductosService.php';
require __DIR__ . '/services/CategoriasService.php';
require __DIR__ . '/services/FraganciasService.php';

use config\Config;
use services\SessionService;
use services\ProductosService;
use services\CategoriasService;
use services\FraganciasService;

$config = Config::getInstance();
$session = SessionService::getInstance();
if (!$session->hasRole('ADMIN')) {
    header('Location:/index.php');
    exit;
}
$pSrv = new ProductosService($config->db);
$cSrv = new CategoriasService($config->db);
$fSrv = new FraganciasService($config->db);
$id = (int)($_GET['id'] ?? 0);
$p = $pSrv->findById($id);
if (!$p) {
    header('Location:/index.php');
    exit;
}
$categorias = $cSrv->findAll();
$fragancias = $fSrv->findAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'precio' => (float)($_POST['precio'] ?? 0),
        'stock' => (int)($_POST['stock'] ?? 0),
        'categoria_id' => ($_POST['categoria_id'] ?? '') ?: null,
        'fragancia_id' => ($_POST['fragancia_id'] ?? '') ?: null,
    ];
    if ($data['nombre'] === '') $errors['nombre'] = 'Requerido';
    if ($data['precio'] < 0) $errors['precio'] = 'Precio inválido';
    if (empty($errors)) {
        $pSrv->update($id, $data);
        header('Location:/details.php?id=' . $id);
        exit;
    }
}
include __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Editar producto #<?= $p->id ?></h1>
<form method="post">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre*</label>
            <input name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? $p->nombre) ?>">
            <?php if (isset($errors['nombre'])): ?><div class="text-danger small"><?= $errors['nombre'] ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label">Precio*</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= htmlspecialchars($_POST['precio'] ?? $p->precio) ?>">
            <?php if (isset($errors['precio'])): ?><div class="text-danger small"><?= $errors['precio'] ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($_POST['stock'] ?? $p->stock) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($categorias as $c): ?><option value="<?= $c->id ?>" <?= (($p->categoriaId ?? '') === $c->id || (($_POST['categoria_id'] ?? '') === $c->id)) ? 'selected' : '' ?>><?= htmlspecialchars($c->nombre) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Fragancia</label>
            <select name="fragancia_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($fragancias as $f): ?><option value="<?= $f->id ?>" <?= (($p->fraganciaId ?? '') === $f->id || (($_POST['fragancia_id'] ?? '') === $f->id)) ? 'selected' : '' ?>><?= htmlspecialchars($f->nombre) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? $p->descripcion) ?></textarea>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-secondary" href="/details.php?id=<?= $p->id ?>">Cancelar</a>
        <button class="btn btn-primary">Guardar cambios</button>
    </div>
</form>
<?php include __DIR__ . '/footer.php';
