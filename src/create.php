<?php
use config\Config;
use services\SessionService;
use services\ProductosService;
use services\CategoriasService;
use services\FraganciasService;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/Config.php';
require __DIR__ . '/services/SessionService.php';
require __DIR__ . '/services/ProductosService.php';
require __DIR__ . '/services/CategoriasService.php';
require __DIR__ . '/services/FraganciasService.php';
// require_once dirname(__DIR__) . '/../vendor/autoload.php';



$config = Config::getInstance();
$session = SessionService::getInstance();


// $categoriasService = new CategoriasService($config->db);
// $productosService = new ProductosService($config->db);
// $fraganciasService = new FraganciasService($config->db);

if (!$session->hasRole('ADMIN')) {
    header('Location: /index.php');
    exit;
}
$pSrv = new ProductosService($config->db);
$cSrv = new CategoriasService($config->db);
$fSrv = new FraganciasService($config->db);
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
        'imagen' => null,
    ];
    if ($data['nombre'] === '') $errors['nombre'] = 'Requerido';
    if ($data['precio'] < 0) $errors['precio'] = 'Precio inválido';
    if (empty($errors)) {
        $id = $pSrv->save($data);
        header('Location: /update-image.php?id=' . $id);
        exit;
    }
}
include __DIR__ . '/header.php';
?>
<h1 class="h3 mb-3">Nuevo producto</h1>
<form method="post">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre*</label>
            <input name="nombre" class="form-control" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            <?php if (isset($errors['nombre'])): ?><div class="text-danger small"><?= $errors['nombre'] ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Precio*</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= htmlspecialchars($_POST['precio'] ?? '0') ?>">
            <?php if (isset($errors['precio'])): ?><div class="text-danger small"><?= $errors['precio'] ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($categorias as $c): ?><option value="<?= $c->id ?>" <?= (($_POST['categoria_id'] ?? '') === $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->nombre) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Fragancia</label>
            <select name="fragancia_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($fragancias as $f): ?><option value="<?= $f->id ?>" <?= (($_POST['fragancia_id'] ?? '') === $f->id) ? 'selected' : '' ?>><?= htmlspecialchars($f->nombre) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-secondary" href="/index.php">Cancelar</a>
        <button class="btn btn-success">Guardar</button>
    </div>
</form>
<?php include __DIR__ . '/footer.php';
