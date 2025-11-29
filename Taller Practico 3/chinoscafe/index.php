<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

$res = $mysqli->query("SELECT COUNT(*) as c FROM products");
$prod = $res->fetch_assoc()['c'] ?? 0;
$res = $mysqli->query("SELECT COUNT(*) as c FROM suppliers");
$sup = $res->fetch_assoc()['c'] ?? 0;
$res = $mysqli->query("SELECT COALESCE(SUM(total),0) as s FROM sales");
$sales_sum = $res->fetch_assoc()['s'] ?? 0;

include 'header.php';
?>

<!-- ====== BOTÓN PEQUEÑO DE SINCRONIZACIÓN ====== -->
<div class="container-fluid" style="margin-top: 15px;">
  <div class="d-flex justify-content-start">
    <button id="syncBtn" class="btn btn-warning btn-sm" title="Sincronizar con la VM" style="border-radius: 50%;">
      🔁
    </button>
  </div>
</div>

<!-- Alerta visual -->
<div id="syncAlert" class="alert alert-info text-center" role="alert" style="display:none; margin-top:10px;"></div>

<!-- ====== CONTENIDO PRINCIPAL ====== -->
<div class="container mt-3">
  <div class="row">
    <div class="col-md-4">
      <div class="card mb-3"><div class="card-body">
        <h5 class="card-title">Productos</h5>
        <p class="card-text display-6"><?php echo intval($prod); ?></p>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3"><div class="card-body">
        <h5 class="card-title">Proveedores</h5>
        <p class="card-text display-6"><?php echo intval($sup); ?></p>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3"><div class="card-body">
        <h5 class="card-title">Ventas totales</h5>
        <p class="card-text display-6">B/. <?php echo number_format($sales_sum,2); ?></p>
      </div></div>
    </div>
  </div>

  <h5 class="mt-3">Productos con bajo stock</h5>
  <table class="table table-sm">
    <thead><tr><th>Producto</th><th>Stock</th><th>Precio</th></tr></thead>
    <tbody>
    <?php
    $q = $mysqli->query("SELECT id, name, stock, price FROM products ORDER BY stock ASC LIMIT 8");
    while($r = $q->fetch_assoc()){
      $badge = $r['stock'] <= 3 ? '<span class="badge badge-low">Bajo</span>' : '';
      echo "<tr><td>".htmlspecialchars($r['name'])." $badge</td><td>{$r['stock']}</td><td>B/.".number_format($r['price'],2)."</td></tr>";
    }
    ?>
    </tbody>
  </table>
</div>

<!-- ====== SCRIPTS DE SINCRONIZACIÓN ====== -->
<script>
document.getElementById("syncBtn").addEventListener("click", function() {
  const alertBox = document.getElementById("syncAlert");
  alertBox.style.display = "block";
  alertBox.className = "alert alert-info text-center";
  alertBox.innerHTML = "🔄 Sincronizando con la máquina virtual...";

  fetch("sync_vm.php")
    .then(response => response.text())
    .then(text => {
      if (text.includes("✅") || text.includes("Sincronización completada")) {
        alertBox.className = "alert alert-success text-center";
        alertBox.innerHTML = "✅ Sincronización completada con éxito.";
      } else {
        alertBox.className = "alert alert-warning text-center";
        alertBox.innerHTML = "⚠️ Sincronización finalizada con advertencias.";
      }
      setTimeout(() => alertBox.style.display = "none", 4000);
    })
    .catch(err => {
      alertBox.className = "alert alert-danger text-center";
      alertBox.innerHTML = "❌ Error al conectar con la VM.";
      setTimeout(() => alertBox.style.display = "none", 4000);
    });
});
</script>

<?php include 'footer.php'; ?>
