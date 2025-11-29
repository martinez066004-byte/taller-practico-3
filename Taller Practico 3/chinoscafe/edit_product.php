<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();
require_once __DIR__ . '/utils/replicate_helper.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: products.php'); exit; }

// POST: guardar cambios (this page handles saving as well)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'] ?? '';
    $supplier_id = isset($_POST['supplier_id']) && $_POST['supplier_id']!=='' ? intval($_POST['supplier_id']) : null;

    $stmt = $mysqli->prepare("UPDATE products SET code=?, name=?, price=?, stock=?, description=?, supplier_id=? WHERE id=?");
    $stmt->bind_param('ssdisii', $code, $name, $price, $stock, $description, $supplier_id, $id);
    if ($stmt->execute()) {
        replicate_query("UPDATE products SET code=?, name=?, price=?, stock=?, description=?, supplier_id=? WHERE id=?", [$code, $name, $price, $stock, $description, $supplier_id, $id]);
        flash_set('success','Producto actualizado.');
        header('Location: products.php');
        exit;
    } else {
        flash_set('error','Error al actualizar: '.$stmt->error);
    }
    $stmt->close();
}

$res = $mysqli->query("SELECT * FROM products WHERE id = $id LIMIT 1");
if (!$res || $res->num_rows == 0) { flash_set('error','Producto no encontrado.'); header('Location: products.php'); exit; }
$p = $res->fetch_assoc();
$suppliers = $mysqli->query("SELECT id, name FROM suppliers ORDER BY name");

include 'header.php';
?>
<h3>Editar producto</h3>
<form method="post">
  <div class="mb-3"><label>Código</label><input class="form-control" name="code" value="<?php echo htmlspecialchars($p['code']); ?>" required></div>
  <div class="mb-3"><label>Nombre</label><input class="form-control" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required></div>
  <div class="mb-3"><label>Proveedor</label>
    <select class="form-select" name="supplier_id">
      <option value="">-- Ninguno --</option>
      <?php while($s = $suppliers->fetch_assoc()){ $sel = $s['id']==$p['supplier_id'] ? 'selected':''; echo "<option value='{$s['id']}' $sel>".htmlspecialchars($s['name'])."</option>"; } ?>
    </select>
  </div>
  <div class="mb-3"><label>Precio</label><input class="form-control" type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($p['price']); ?>"></div>
  <div class="mb-3"><label>Stock</label><input class="form-control" type="number" name="stock" value="<?php echo htmlspecialchars($p['stock']); ?>"></div>
  <div class="mb-3"><label>Descripción</label><textarea class="form-control" name="description"><?php echo htmlspecialchars($p['description']); ?></textarea></div>
  <button class="btn btn-primary">Guardar cambios</button>
  <a class="btn btn-secondary" href="products.php">Cancelar</a>
</form>
<?php include 'footer.php'; ?>