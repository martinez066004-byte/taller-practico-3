<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ? intval($_POST['supplier_id']) : null;
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'] ?? '';

    $stmt = $mysqli->prepare("INSERT INTO products (supplier_id, code, name, price, stock, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdis', $supplier_id, $code, $name, $price, $stock, $description);
    if ($stmt->execute()) {
        flash_set('success','Producto agregado.');
        header('Location: products.php');
        exit;
    } else {
        flash_set('error','Error al guardar: '.$mysqli->error);
    }
}

$suppliers = $mysqli->query("SELECT id, name FROM suppliers ORDER BY name");

include 'header.php';
?>
<h3>Nuevo producto</h3>
<form method="post">
  <div class="mb-3"><label>Código</label><input class="form-control" name="code" required></div>
  <div class="mb-3"><label>Nombre</label><input class="form-control" name="name" required></div>
  <div class="mb-3"><label>Proveedor</label>
    <select class="form-select" name="supplier_id">
      <option value="">-- Ninguno --</option>
      <?php while($s = $suppliers->fetch_assoc()){ echo "<option value='{$s['id']}'>".htmlspecialchars($s['name'])."</option>"; } ?>
    </select>
  </div>
  <div class="mb-3"><label>Precio</label><input class="form-control" type="number" step="0.01" name="price" required></div>
  <div class="mb-3"><label>Stock</label><input class="form-control" type="number" name="stock" required></div>
  <div class="mb-3"><label>Descripción</label><textarea class="form-control" name="description"></textarea></div>
  <button class="btn btn-primary">Guardar</button>
  <a class="btn btn-secondary" href="products.php">Volver</a>
</form>
<?php include 'footer.php'; ?>