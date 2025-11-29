<?php
require_once 'db.php';
require_once 'auth.php';

// Solo permitir si no hay usuarios
$res = $mysqli->query("SELECT COUNT(*) as c FROM users");
$count = $res->fetch_assoc()['c'] ?? 0;
if ($count > 0) {
    flash_set('warning','Registro deshabilitado. Ya existe un usuario.');
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['full_name']);

    if (strlen($username) < 3 || strlen($password) < 6) {
        flash_set('error','Usuario o contraseña demasiado corta (contraseña >= 6 caracteres).');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, full_name) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $hash, $fullname);
        if ($stmt->execute()) {
            flash_set('success','Usuario administrador creado. Inicia sesión.');
            header('Location: login.php');
            exit;
        } else {
            flash_set('error','Error al crear el usuario: ' . $mysqli->error);
        }
    }
}

include 'header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4>Crear cuenta administrador</h4>
        <form method="post">
          <div class="mb-3"><label>Nombre completo</label><input class="form-control" name="full_name" required></div>
          <div class="mb-3"><label>Usuario</label><input class="form-control" name="username" required></div>
          <div class="mb-3"><label>Contraseña</label><input class="form-control" name="password" type="password" required></div>
          <button class="btn btn-success">Crear admin</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>