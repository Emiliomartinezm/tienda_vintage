<?php
session_start();
require 'db.php';

$errores = [];
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validaciones básicas
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresa un correo válido.';
    }

    if (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // Si no hay errores hasta aquí, revisamos si el correo ya existe
    if (empty($errores)) {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = 'Ya existe una cuenta con ese correo.';
        } else {
            // Insertar usuario nuevo
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare(
                'INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt_insert->bind_param('sss', $nombre, $email, $hash);

            if ($stmt_insert->execute()) {
                // Opcional: iniciar sesión automáticamente
                $_SESSION['usuario_id'] = $stmt_insert->insert_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_rol'] = 'cliente';

                // Mensaje de éxito o redirección a inicio / catálogo
                // $exito = 'Cuenta creada correctamente. Ya puedes iniciar sesión.';
                header('Location: index.php');
                exit;
            } else {
                $errores[] = 'Error al crear la cuenta. Intenta más tarde.';
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Crear cuenta - Vintage Sound Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Vintage Sound</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
        <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
        <li class="nav-item"><a class="btn btn-outline-light ms-2" href="carrito.php">🛒 Carrito</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-5" style="max-width: 600px;">
  <h1 class="mb-4">Crear cuenta</h1>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($exito): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($exito) ?>
    </div>
  <?php endif; ?>

  <form method="post" action="registro.php">
    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control"
             value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Repetir contraseña</label>
      <input type="password" name="password2" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
  </form>

  <p class="mt-3 text-center">
    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>.
  </p>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
