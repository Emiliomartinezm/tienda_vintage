<?php
session_start();
require 'db.php';

// Obtener productos de la BD
$query = "SELECT * FROM productos WHERE activo = 1 AND stock > 0";
$resultado = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo - Vintage Sound Store</title>
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
        <li class="nav-item"><a class="nav-link active" href="catalogo.php">Catálogo</a></li>
        <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
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
  <h1 class="mb-4 text-center">Nuestro Catálogo</h1>

  <?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'compra_exitosa'): ?>
    <div class="alert alert-success text-center">
        ¡Gracias por tu compra! Tu pedido ha sido procesado.
    </div>
  <?php endif; ?>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while($prod = mysqli_fetch_assoc($resultado)): ?>
    <div class="col">
      <div class="card h-100 shadow-sm">
        <!-- Imagen del producto -->
        <img src="<?= htmlspecialchars($prod['imagen_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['nombre']) ?>" style="height: 250px; object-fit: cover;">
        
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($prod['nombre']) ?></h5>
          <p class="card-text text-muted"><?= htmlspecialchars($prod['descripcion']) ?></p>
          
          <div class="mt-auto">
            <h4 class="text-primary fw-bold">$<?= number_format($prod['precio'], 2) ?></h4>
            <p class="small text-secondary">Stock disponible: <?= $prod['stock'] ?></p>

            <!-- Formulario para agregar al carrito -->
            <form action="acciones_carrito.php" method="POST">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id_producto" value="<?= $prod['id'] ?>">
                
                <div class="d-flex gap-2">
                    <input type="number" name="cantidad" value="1" min="1" max="<?= $prod['stock'] ?>" class="form-control w-25">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <button type="submit" class="btn btn-success flex-grow-1">Agregar al carrito</button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary flex-grow-1">Inicia sesión para comprar</a>
                    <?php endif; ?>
                </div>
            </form>

          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <div class="text-center mt-4">
      <a href="index.php" class="btn btn-secondary">← Regresar al inicio</a>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>