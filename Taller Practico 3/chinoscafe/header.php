<?php
require_once 'db.php';
require_once 'auth.php';
$flash = flash_get();
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Chinos Café</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f4c81;">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Chinos Café</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="suppliers.php">Proveedores</a></li>
        <li class="nav-item"><a class="nav-link" href="sales.php">Ventas</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contacto</a></li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if ($user): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Ingresar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?php echo ($flash['type']==='error' ? 'danger' : ($flash['type']==='warning'?'warning':'success')); ?>">
      <?php echo htmlspecialchars($flash['msg']); ?>
    </div>
  <?php endif; ?>