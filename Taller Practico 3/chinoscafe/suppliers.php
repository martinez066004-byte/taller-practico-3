<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();
require_once __DIR__ . '/utils/replicate_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_supplier') {
    $name = trim($_POST['name']);
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    $stmt = $mysqli->prepare("INSERT INTO suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $phone, $email, $address);
    if ($stmt->execute()) {
        $new_id = $mysqli->insert_id;
        $created_at = date('Y-m-d H:i:s');
        $sqlr = "INSERT INTO suppliers (id, name, phone, email, address, created_at)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE name=VALUES(name), phone=VALUES(phone), email=VALUES(email), address=VALUES(address)";
        replicate_query($sqlr, [$new_id, $name, $phone, $email, $address, $created_at]);
        flash_set('success','Proveedor agregado.');
    } else {
        flash_set('error','Error al agregar: ' . $stmt->error);
    }
    $stmt->close();
    header('Location: suppliers.php');
    exit;
}

$suppliers = $mysqli->query("SELECT * FROM suppliers ORDER BY name");
include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Proveedores</h3>
</div>

<table class="table table-sm">
  <thead><tr><th>Nombre</th><th>Tel</th><th>Email</th><th>Dirección</th></tr></thead>
  <tbody>
    <?php while($s = $suppliers->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['phone']); ?></td>
        <td><?php echo htmlspecialchars($s['email']); ?></td>
        <td><?php echo htmlspecialchars($s['address']); ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<h5 class="mt-4">Agregar proveedor</h5>
<form method="post">
  <input type="hidden" name="action" value="add_supplier">
  <div class="row">
    <div class="col-md-4 mb-3"><input class="form-control" name="name" placeholder="Nombre" required></div>
    <div class="col-md-2 mb-3"><input class="form-control" name="phone" placeholder="Teléfono"></div>
    <div class="col-md-3 mb-3"><input class="form-control" name="email" placeholder="Email" type="email"></div>
    <div class="col-md-3 mb-3"><input class="form-control" name="address" placeholder="Dirección"></div>
  </div>
  <button class="btn btn-primary">Agregar</button>
</form>

<?php include 'footer.php'; ?>