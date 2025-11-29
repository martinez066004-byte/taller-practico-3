<?php
require_once 'db.php';
require_once 'auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message']);
    if ($stmt->execute()) flash_set('success','Mensaje guardado. Gracias.');
    else flash_set('error','Error al guardar mensaje.');
    header('Location: contact.php');
    exit;
}

include 'header.php';
?>
<div class="row">
  <div class="col-md-7">
    <h3>Contacto</h3>
    <form method="post">
      <div class="mb-3"><label>Nombre</label><input class="form-control" name="name" required></div>
      <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required></div>
      <div class="mb-3"><label>Teléfono</label><input class="form-control" name="phone"></div>
      <div class="mb-3"><label>Mensaje</label><textarea class="form-control" name="message" required></textarea></div>
      <button class="btn btn-primary">Enviar</button>
    </form>
  </div>
  <div class="col-md-5">
    <h5>Información</h5>
    <p class="small text-muted">Para contactar a otro lugar/provedoor (esto es mas como... decoracion)</p>
  </div>
</div>
<?php include 'footer.php'; ?>
