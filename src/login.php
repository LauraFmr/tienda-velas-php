<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config/Config.php';
require __DIR__ . '/services/UsersService.php';
require __DIR__ . '/services/SessionService.php';

use config\Config;
use services\UsersService;
use services\SessionService;

$config = Config::getInstance();
$uSrv = new UsersService($config->db);
$session = SessionService::getInstance();


$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    try {
        $user = $uSrv->authenticate($username, $password);
        $session->login([
            'id' => $user->id,
            'username' => $user->username,
            'roles' => $user->roles,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido
        ]);
        header('Location: /index.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
include __DIR__ . '/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h3 mb-3">Iniciar sesión</h1>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">Usuario</label><input class="form-control" name="username" required></div>
            <div class="mb-3"><label class="form-label">Contraseña</label><input class="form-control" type="password" name="password" required></div>
            <button class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/footer.php';
