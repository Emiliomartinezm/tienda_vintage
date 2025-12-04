<?php
session_start();
require 'db.php';

$mensaje_estado = '';
$tipo_alerta = '';

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']);
    $email   = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    if ($nombre && $email && $mensaje) {
        // Insertar en la base de datos
        $sql = "INSERT INTO mensajes (nombre, email, mensaje) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $mensaje);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensaje_estado = "Mensaje enviado correctamente.";
            $tipo_alerta = "success";
        } else {
            $mensaje_estado = "Error al enviar. Intenta de nuevo.";
            $tipo_alerta = "danger";
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje_estado = "Completa todos los campos.";
        $tipo_alerta = "warning";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Contacto - Vintage Sound Store</title>
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
        <li class="nav-item"><a class="nav-link active" href="contacto.php">Contacto</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <li class="nav-item"><span class="nav-link text-warning">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="btn btn-outline-light ms-2" href="carrito.php">Carrito</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-5">
    <h2 class="mb-4">Formulario de Contacto</h2>

    <?php if ($mensaje_estado): ?>
        <div class="alert alert-<?= $tipo_alerta ?>">
            <?= $mensaje_estado ?>
        </div>
    <?php endif; ?>

    <form action="contacto.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Mensaje:</label>
            <textarea name="mensaje" class="form-control" rows="4" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

    <!-- Información de Contacto Agregada -->
    <div class="mt-5 pt-4 border-top text-center">
        <h4>O encuéntranos aquí:</h4>
        <p class="mb-1"><strong>Dirección:</strong> Avenida de la Música 123</p>
        <p class="mb-1"><strong>Correo:</strong> tiendavintage@gmail.com</p>
        <p class="mb-0"><strong>Teléfono:</strong> +52 55 3198 2054</p>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>