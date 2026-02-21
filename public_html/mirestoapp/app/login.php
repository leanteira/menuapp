<?php

require_once __DIR__ . '/mr_auth.php';

if (mr_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    if ($email === '' || $password === '') {
        $error = 'Completá email y contraseña.';
    } else {
        $conn = mr_db();
        $sql = 'SELECT id, restaurante_id, nombre, email, password, rol, activo FROM usuarios WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $passwordOk = false;
        if ($user) {
            $storedPassword = (string) $user['password'];
            $passwordOk = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);
        }

        if (!$user || !$passwordOk) {
            $error = 'Credenciales inválidas.';
        } elseif ((int) $user['activo'] !== 1) {
            $error = 'Tu usuario está inactivo.';
        } else {
            $_SESSION['mr_user_id'] = (int) $user['id'];
            $_SESSION['mr_restaurante_id'] = $user['restaurante_id'] !== null ? (int) $user['restaurante_id'] : null;
            $_SESSION['mr_nombre'] = $user['nombre'];
            $_SESSION['mr_email'] = $user['email'];
            $_SESSION['mr_rol'] = $user['rol'];

            $_SESSION['id'] = (int) $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['apellido'] = '';
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="assets/vendor/fonts/remixicon/remixicon.css">
  <link rel="stylesheet" href="assets/vendor/css/rtl/core.css">
  <link rel="stylesheet" href="assets/vendor/css/rtl/theme-semi-dark.css">
  <link rel="stylesheet" href="assets/css/demo.css">
  <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css">
</head>
<body>
  <div class="authentication-wrapper authentication-basic container-p-y p-4">
    <div class="authentication-inner py-4">
      <div class="card p-4">
        <div class="card-body">
          <h4 class="mb-2">MiRestoApp · Panel</h4>
          <p class="mb-4">Ingresá con usuario de la tabla usuarios.</p>

          <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="post" action="">
            <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <input class="form-control" type="email" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Contraseña</label>
              <input class="form-control" type="password" id="password" name="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
