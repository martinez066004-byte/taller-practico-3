<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

$id = intval($_GET['id'] ?? 0);
if (!$id) { flash_set('error','ID de factura faltante'); header('Location: sales.php'); exit; }
$saleRes = $mysqli->query("SELECT s.*, u.full_name as user_name FROM sales s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = $id");
if (!$saleRes || $saleRes->num_rows == 0) { flash_set('error','Venta no encontrada'); header('Location: sales.php'); exit; }
$sale = $saleRes->fetch_assoc();
$items = $mysqli->query("SELECT si.*, p.name FROM sale_items si JOIN products p ON p.id = si.product_id WHERE si.sale_id = $id");

include 'header.php';
?>
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <div>
        <h4>Factura: <?php echo htmlspecialchars($sale['invoice_number']); ?></h4>
        <p class="mb-0"><strong>Fecha:</strong> <?php echo $sale['date']; ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($sale['customer_name']); ?></p>
      </div>
      <div class="text-end">
        <h5>Total</h5>
        <h3>B/. <?php echo number_format($sale['total'],2); ?></h3>
        <p class="small text-muted">Registrado por: <?php echo htmlspecialchars($sale['user_name'] ?: 'N/A'); ?></p>
      </div>
    </div>

    <table class="table mt-3">
      <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
      <tbody>
      <?php while($it = $items->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($it['name']); ?></td>
          <td><?php echo intval($it['qty']); ?></td>
          <td>B/. <?php echo number_format($it['price'],2); ?></td>
          <td>B/. <?php echo number_format($it['subtotal'],2); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <a class="btn btn-secondary" href="sales.php">Volver a ventas</a>
  </div>
</div>
<?php include 'footer.php'; ?>