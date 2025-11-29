<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();
require_once __DIR__ . '/utils/replicate_helper.php';

// ADD PRODUCT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_product') {
    $supplier_id = isset($_POST['supplier_id']) && $_POST['supplier_id']!=='' ? intval($_POST['supplier_id']) : null;
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'] ?? '';

    $stmt = $mysqli->prepare("INSERT INTO products (supplier_id, code, name, price, stock, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdis', $supplier_id, $code, $name, $price, $stock, $description);
    if ($stmt->execute()) {
        $new_id = $mysqli->insert_id;
        // replicar remoto incluyendo id y created_at
        $created_at = date('Y-m-d H:i:s');
        $sqlr = "INSERT INTO products (id, supplier_id, code, name, price, stock, description, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE supplier_id=VALUES(supplier_id), code=VALUES(code), name=VALUES(name), price=VALUES(price), stock=VALUES(stock), description=VALUES(description)";
        replicate_query($sqlr, [$new_id, $supplier_id, $code, $name, $price, $stock, $description, $created_at]);
        flash_set('success','Producto agregado.');
    } else {
        flash_set('error','Error al agregar: ' . $stmt->error);
    }
    $stmt->close();
    header('Location: products.php');
    exit;
}

// EDIT PRODUCT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_product') {
    $id = intval($_POST['id']);
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'] ?? '';
    $supplier_id = isset($_POST['supplier_id']) && $_POST['supplier_id']!=='' ? intval($_POST['supplier_id']) : null;

    $stmt = $mysqli->prepare("UPDATE products SET code=?, name=?, price=?, stock=?, description=?, supplier_id=? WHERE id=?");
    // tipos: s s d i s i i
    $stmt->bind_param('ssdisii', $code, $name, $price, $stock, $description, $supplier_id, $id);
    if ($stmt->execute()) {
        $sqlr = "UPDATE products SET code=?, name=?, price=?, stock=?, description=?, supplier_id=? WHERE id=?";
        replicate_query($sqlr, [$code, $name, $price, $stock, $description, $supplier_id, $id]);
        flash_set('success','Producto actualizado.');
    } else {
        flash_set('error','Error al actualizar: ' . $stmt->error);
    }
    $stmt->close();
    header('Location: products.php');
    exit;
}

// DELETE PRODUCT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_product') {
    $id = intval($_POST['id']);
    // eliminar sale_items locales relacionados
    $del = $mysqli->prepare("DELETE FROM sale_items WHERE product_id = ?");
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();

    $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        replicate_query("DELETE FROM sale_items WHERE product_id = ?", [$id]);
        replicate_query("DELETE FROM products WHERE id = ?", [$id]);
        flash_set('success','Producto eliminado.');
    } else {
        flash_set('error','Error al eliminar: ' . $stmt->error);
    }
    $stmt->close();
    header('Location: products.php');
    exit;
}

// LIST
$qsearch = trim($_GET['q'] ?? '');
$where = '';
if ($qsearch !== '') {
    $esc = $mysqli->real_escape_string($qsearch);
    $where = "WHERE p.name LIKE '%{$esc}%' OR p.code LIKE '%{$esc}%'";
}
$products = $mysqli->query("SELECT p.*, s.name as supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id $where ORDER BY p.name LIMIT 200");
$suppliers = $mysqli->query("SELECT id, name FROM suppliers ORDER BY name");

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Productos</h3>
  <div><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">Nuevo producto</button></div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto"><input name="q" value="<?php echo htmlspecialchars($qsearch); ?>" class="form-control" placeholder="Buscar por nombre o código"></div>
  <div class="col-auto"><button class="btn btn-outline-primary">Buscar</button></div>
</form>

<table class="table table-striped">
  <thead><tr><th>Código</th><th>Nombre</th><th>Proveedor</th><th>Precio</th><th>Stock</th><th></th></tr></thead>
  <tbody>
    <?php while($p = $products->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($p['code']); ?></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo htmlspecialchars($p['supplier_name']); ?></td>
        <td><?php echo number_format($p['price'],2); ?></td>
        <td><?php echo intval($p['stock']); ?></td>
        <td>
          <a class="btn btn-sm btn-outline-secondary" href="edit_product.php?id=<?php echo $p['id']; ?>">Editar</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Eliminar producto?');">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <input type="hidden" name="action" value="delete_product">
            <button class="btn btn-sm btn-danger">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<!-- Modal Add -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
      <input type="hidden" name="action" value="add_product">
      <div class="modal-header"><h5 class="modal-title">Nuevo producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label>Código</label><input class="form-control" name="code" required></div>
        <div class="mb-2"><label>Nombre</label><input class="form-control" name="name" required></div>
        <div class="mb-2"><label>Proveedor</label>
          <select class="form-select" name="supplier_id">
            <option value="">-- Ninguno --</option>
            <?php
            $suppliers->data_seek(0);
            while($s = $suppliers->fetch_assoc()){
              echo "<option value='{$s['id']}'>".htmlspecialchars($s['name'])."</option>";
            }
            ?>
          </select>
        </div>
        <div class="mb-2"><label>Precio</label><input class="form-control" name="price" type="number" step="0.01" required></div>
        <div class="mb-2"><label>Stock</label><input class="form-control" name="stock" type="number" required></div>
        <div class="mb-2"><label>Descripción</label><textarea class="form-control" name="description"></textarea></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>