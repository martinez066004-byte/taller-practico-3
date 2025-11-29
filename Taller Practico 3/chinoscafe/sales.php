<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();
require_once __DIR__ . '/utils/replicate_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_sale') {
    $product_ids = $_POST['product_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $customer = trim($_POST['customer_name'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;

    $mysqli->begin_transaction();
    try {
        $total = 0.0;
        // validar y actualizar stock local
        foreach ($product_ids as $i => $pid) {
            $pid = intval($pid);
            $q = intval($qtys[$i]);
            if ($q <= 0) continue;
            $res = $mysqli->query("SELECT price, stock FROM products WHERE id = $pid FOR UPDATE");
            if (!$res || $res->num_rows == 0) throw new Exception("Producto no encontrado (ID $pid).");
            $row = $res->fetch_assoc();
            if ($row['stock'] < $q) throw new Exception("Stock insuficiente para producto ID $pid.");
            $subtotal = floatval($row['price']) * $q;
            $total += $subtotal;
            // reducir stock local
            $stmt = $mysqli->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param('ii', $q, $pid);
            $stmt->execute();
            $stmt->close();
        }

        // crear venta local
        $invoice_num = 'INV' . time();
        $stmt = $mysqli->prepare("INSERT INTO sales (invoice_number, total, user_id, customer_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sdss', $invoice_num, $total, $user_id, $customer);
        $stmt->execute();
        $sale_id = $mysqli->insert_id;
        $stmt->close();

        // insertar items locales
        foreach ($product_ids as $i => $pid) {
            $pid = intval($pid);
            $q = intval($qtys[$i]);
            if ($q <= 0) continue;
            $res = $mysqli->query("SELECT price FROM products WHERE id = $pid");
            $price = $res->fetch_assoc()['price'];
            $subtotal = $price * $q;
            $stmt = $mysqli->prepare("INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iiidd', $sale_id, $pid, $q, $price, $subtotal);
            $stmt->execute();
            $stmt->close();
        }

        $mysqli->commit();

        // -- replicar en remoto (intentar)
        if ($mysqli_remote) {
            // replicar venta con mismo id y fecha
            $resSale = $mysqli->query("SELECT * FROM sales WHERE id = $sale_id LIMIT 1");
            $saleRow = $resSale->fetch_assoc();
            replicate_query(
                "INSERT INTO sales (id, invoice_number, total, date, user_id, customer_name)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE invoice_number=VALUES(invoice_number), total=VALUES(total), date=VALUES(date), user_id=VALUES(user_id), customer_name=VALUES(customer_name)",
                [$saleRow['id'], $saleRow['invoice_number'], $saleRow['total'], $saleRow['date'], $saleRow['user_id'] ?? null, $saleRow['customer_name']]
            );

            // replicar items y ajustar stock remoto
            $resItems = $mysqli->query("SELECT * FROM sale_items WHERE sale_id = $sale_id");
            while ($it = $resItems->fetch_assoc()) {
                replicate_query(
                    "INSERT INTO sale_items (id, sale_id, product_id, qty, price, subtotal)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE sale_id=VALUES(sale_id), product_id=VALUES(product_id), qty=VALUES(qty), price=VALUES(price), subtotal=VALUES(subtotal)",
                    [$it['id'], $it['sale_id'], $it['product_id'], $it['qty'], $it['price'], $it['subtotal']]
                );
                // restar stock remoto
                replicate_query("UPDATE products SET stock = stock - ? WHERE id = ?", [$it['qty'], $it['product_id']]);
            }
        } else {
            error_log("[REPLICATE] No hay conexión remota: venta no replicada.");
        }

        flash_set('success','Venta registrada.');
        header("Location: invoice.php?id={$sale_id}");
        exit;

    } catch (Exception $e) {
        $mysqli->rollback();
        flash_set('error','Error en la venta: ' . $e->getMessage());
        header('Location: sales.php');
        exit;
    }
}

$products_res = $mysqli->query("SELECT id, name, price, stock FROM products ORDER BY name");
$sales_res = $mysqli->query("SELECT * FROM sales ORDER BY date DESC LIMIT 10");

include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Registrar Venta</h3>
  <a class="btn btn-sm btn-outline-secondary" href="create_sale.php">Crear venta (página dedicada)</a>
</div>

<form method="post" onsubmit="return confirm('Guardar venta?');">
  <input type="hidden" name="action" value="create_sale">
  <div class="mb-3"><label>Cliente (opcional)</label><input class="form-control" name="customer_name"></div>

  <h6>Productos</h6>
  <div id="items">
    <div class="row item-row g-2 align-items-center mb-2">
      <div class="col-md-8">
        <select name="product_id[]" class="form-select">
          <?php
          $products_res->data_seek(0);
          while($p = $products_res->fetch_assoc()){
            echo "<option value='{$p['id']}'>".htmlspecialchars($p['name'])." (Stock: {$p['stock']}) - B/.".number_format($p['price'],2)."</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-2"><input class="form-control" name="qty[]" type="number" value="1" min="1"></div>
      <div class="col-md-2"><button class="btn btn-sm btn-danger removeRow" type="button">Quitar</button></div>
    </div>
  </div>

  <p><a class="btn btn-sm btn-secondary" href="#" id="addRow">Agregar fila</a></p>
  <button class="btn btn-primary">Guardar Venta</button>
</form>

<h5 class="mt-4">Ventas recientes</h5>
<table class="table table-sm">
  <thead><tr><th>Factura</th><th>Total</th><th>Fecha</th><th>Acción</th></tr></thead>
  <tbody>
    <?php while($s = $sales_res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($s['invoice_number']); ?></td>
        <td>B/. <?php echo number_format($s['total'],2); ?></td>
        <td><?php echo $s['date']; ?></td>
        <td><a class="btn btn-sm btn-outline-primary" href="invoice.php?id=<?php echo $s['id']; ?>">Ver</a></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include 'footer.php'; ?>

<script>
document.getElementById('addRow').addEventListener('click', function(e){
  e.preventDefault();
  const cont = document.getElementById('items');
  const first = cont.querySelector('.item-row');
  const clone = first.cloneNode(true);
  clone.querySelector('input[name="qty[]"]').value = 1;
  cont.appendChild(clone);
});
document.addEventListener('click', function(e){
  if (e.target && e.target.classList.contains('removeRow')) {
    const row = e.target.closest('.item-row');
    if (row) row.remove();
  }
});
</script>