<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

$products = $mysqli->query("SELECT id, name, price, stock FROM products ORDER BY name");
include 'header.php';
?>
<h3>Nueva Venta (página dedicada)</h3>
<form method="post" action="sales.php">
  <input type="hidden" name="action" value="create_sale">
  <div class="mb-3"><label>Cliente</label><input class="form-control" name="customer_name"></div>
  <div id="items">
    <div class="row item-row g-2 align-items-center mb-2">
      <div class="col-md-8">
        <select name="product_id[]" class="form-select">
          <?php while($p = $products->fetch_assoc()){ echo "<option value='{$p['id']}'>".htmlspecialchars($p['name'])." (Stock: {$p['stock']}) - B/.".number_format($p['price'],2)."</option>"; } ?>
        </select>
      </div>
      <div class="col-md-2"><input class="form-control" name="qty[]" type="number" value="1" min="1"></div>
      <div class="col-md-2"><button class="btn btn-sm btn-danger removeRow" type="button">Quitar</button></div>
    </div>
  </div>
  <p><a class="btn btn-sm btn-secondary" href="#" id="addRow">Agregar fila</a></p>
  <button class="btn btn-primary">Guardar Venta</button>
</form>
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