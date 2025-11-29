<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

include 'header.php';
?>
<h3>Estado de replicación</h3>
<?php if ($mysqli_remote): ?>
  <div class="alert alert-success">Conectado a la VM remota (<?php echo htmlspecialchars($mysqli_remote->host_info); ?>)</div>
  <p>Puede replicar datos automáticamente desde aquí.</p>
  <form method="post" action="replicate_test_action.php">
    <button class="btn btn-primary">Forzar replicación de prueba (solo tablas básicas)</button>
  </form>
<?php else: ?>
  <div class="alert alert-danger">No hay conexión remota. Verifica la VM y las credenciales en db.php.</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
