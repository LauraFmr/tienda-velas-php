<?php

use services\SessionService;

$session = SessionService::getInstance();
$user = $session->user();
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Candle Mellon</title>
  <link rel="icon" type="image/png" href="/uploads/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --moria: #232428;
      --lothlorien: #2f855a;
      --gold: #d4af37;
    }

    .navbar {
      background: linear-gradient(90deg, var(--moria), #1a1b1e);
      padding: 1rem 0;
    }

    .brand {
      color: var(--gold) !important;
      font-weight: 700;
      letter-spacing: .5px;
      display: flex;
      align-items: center;
    }

    .ring {
      filter: drop-shadow(0 0 6px rgba(212, 175, 55, .5));
    }

    a {
      text-decoration: none
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
      <a class="navbar-brand brand" href="/index.php"><img src="/uploads/logo-candle-mellon.png" alt="Logo" style="height: 50px; margin-right: 12px;"> Candle Mellon - Velas de Fantasía</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nv"><span class="navbar-toggler-icon"></span></button>
      <div id="nv" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="/index.php">Productos</a></li>
          <?php if ($session->hasRole('ADMIN')): ?>
            <li class="nav-item"><a class="nav-link" href="/create.php">Crear</a></li>
          <?php endif; ?>
        </ul>
        <div class="d-flex align-items-center gap-3 text-light">
          <span>👤 <?= $user['username'] ?? 'Invitado' ?></span>
          <?php if ($session->isLoggedIn()): ?>
            <a class="btn btn-sm btn-outline-light" href="/logout.php">Logout</a>
          <?php else: ?>
            <a class="btn btn-sm btn-warning" href="/login.php">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  <div class="container">