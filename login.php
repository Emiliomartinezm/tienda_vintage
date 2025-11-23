<?php
// Mostrar errores para depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php'; // Esto nos trae la variable $conn

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errores[] = 'Ingresa tu correo y contraseña.';
    } else {
        // -------------------------------------------------------
        // CAMBIO IMPORTANTE: Usamos MySQLi (compatible con tu db.php)
        // -------------------------------------------------------
        
        $sql = "SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?";
        
        // Preparamos la consulta
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vinculamos el parámetro ("s" significa string)
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            // Ejecutamos
            mysqli_stmt_execute($stmt);
            
            // Obtenemos el resultado
            $resultado = mysqli_stmt_get_result($stmt);
            
            // Buscamos al usuario
            if ($usuario = mysqli_fetch_assoc($resultado)) {
                // Verificar contraseña
                if (password_verify($password, $usuario['password_hash'])) {
                    // ¡Login Exitoso! Guardamos sesión
                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_email']  = $usuario['email'];
                    $_SESSION['usuario_rol']    = $usuario['rol'];

                    header('Location: index.php');
                    exit;
                } else {
                    $errores[] = 'Correo o contraseña incorrectos.';
                }
            } else {
                $errores[] = 'Correo o contraseña incorrectos.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $errores[] = 'Error en la consulta de base de datos.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión - Vintage Sound Store</title>
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
        <li class="nav-item"><a class="nav-link active" href="login.php">Iniciar sesión</a></li>
        <li class="nav-item"><a class="btn btn-outline-light ms-2" href="carrito.php">Carrito</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-5" style="max-width: 450px;">

  <h1 class="mb-4 text-center">Iniciar sesión</h1>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="login.php">
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" placeholder="tucorreo@mail.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-primary w-100">Entrar</button>
  </form>

  <p class="mt-3 text-center">
    ¿No tienes cuenta?  
    <a href="registro.php" class="fw-bold">Regístrate aquí</a>
  </p>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>