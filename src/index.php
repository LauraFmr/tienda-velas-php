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



$config = Config::getInstance();
$session = SessionService::getInstance();
$pSrv = new ProductosService($config->db);
$cSrv = new CategoriasService($config->db);
$fSrv = new FraganciasService($config->db);


$q = $_GET['q'] ?? null;
$cid = $_GET['categoria'] ?? null;
$fid = $_GET['fragancia'] ?? null;
$productos = $pSrv->findAllWithFilters($q, $cid, $fid);
$categorias = $cSrv->findAll();
$fragancias = $fSrv->findAll();

?>
<?php require_once __DIR__ . '/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Catálogo</h1>
	<?php if ($session->hasRole('ADMIN')): ?><a class="btn btn-success" href="/create.php">+ Nuevo producto</a><?php endif; ?>
</div>
<form class="row g-2 mb-3" method="get">
	<div class="col-sm-4"><input name="q" value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" placeholder="Buscar por nombre o descripción"></div>
	<div class="col-sm-3">
		<select name="categoria" class="form-select">
			<option value="">Todas las categorías</option>
			<?php foreach ($categorias as $c): ?>
				<option value="<?= $c->id ?>" <?= ($cid == $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->nombre) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-sm-3">
		<select name="fragancia" class="form-select">
			<option value="">Todas las fragancias</option>
			<?php foreach ($fragancias as $f): ?>
				<option value="<?= $f->id ?>" <?= $fid === $f->id ? 'selected' : '' ?>><?= htmlspecialchars($f->nombre) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-sm-2"><button class="btn btn-primary w-100">Filtrar</button></div>
</form>


<div class="table-responsive">
	<table class="table table-hover align-middle">
		<thead>
			<tr>
				<th>ID</th>
				<th>Nombre</th>
				<th>Categoría</th>
				<th>Fragancia</th>
				<th>Precio</th>
				<th>Stock</th>
				<th>Imagen</th>
				<th class="text-end">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($productos as $p): ?>
				<tr>
					<td><?= $p->id ?></td>
					<td><?= htmlspecialchars($p->nombre) ?></td>
					<td><?= htmlspecialchars($p->categoriaNombre ?? '-') ?></td>
					<td><?= htmlspecialchars($p->fraganciaNombre ?? '-') ?></td>
					<td><?= number_format($p->precio, 2, ',', '.') ?> €</td>
					<td><?= (int)$p->stock ?></td>
					<td><?php if ($p->imagen): ?><img src="<?= htmlspecialchars($p->imagen) ?>" width="48"><?php else: ?>—<?php endif; ?></td>
					<td class="text-end">
						<a class="btn btn-sm btn-outline-secondary" href="/details.php?id=<?= $p->id ?>">Detalles</a>
						<?php if ($session->hasRole('ADMIN')): ?>
							<a class="btn btn-sm btn-outline-primary" href="/update.php?id=<?= $p->id ?>">Editar</a>
							<a class="btn btn-sm btn-outline-warning" href="/update-image.php?id=<?= $p->id ?>">Imagen</a>
							<a class="btn btn-sm btn-outline-danger" href="/delete.php?id=<?= $p->id ?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include __DIR__ . '/footer.php';
