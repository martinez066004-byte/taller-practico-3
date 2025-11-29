<?php
require_once 'db.php';
require_once 'auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT id, username, password_hash, full_name FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows) {
        $u = $res->fetch_assoc();
        if (password_verify($password, $u['password_hash'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $after = $_SESSION['after_login'] ?? 'index.php';
            unset($_SESSION['after_login']);
            flash_set('success','Bienvenido, '.htmlspecialchars($u['full_name'] ?: $u['username']));
            header("Location: $after");
            exit;
        } else {
            flash_set('error','Contraseña incorrecta.');
        }
    } else {
        flash_set('error','Usuario no encontrado.');
    }
}

include 'header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Iniciar sesión</h4>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input class="form-control" name="username" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input class="form-control" name="password" type="password" required>
          </div>
          <button class="btn btn-primary">Entrar</button>
          <?php
          $r = $mysqli->query("SELECT COUNT(*) as c FROM users");
          $count = $r->fetch_assoc()['c'] ?? 0;
          if ($count == 0) {
              echo '<p class="mt-3 small text-muted">No hay usuarios. <a href="register.php">Crear admin</a></p>';
          }
          ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>