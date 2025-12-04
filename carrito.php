<?php
session_start();
require 'db.php';

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los productos del carrito "abierto"
$query = "SELECT ci.id as id_item, p.nombre, p.imagen_url, ci.precio_unitario, ci.cantidad, (ci.precio_unitario * ci.cantidad) as subtotal 
          FROM carrito_items ci
          JOIN carritos c ON ci.id_carrito = c.id
          JOIN productos p ON ci.id_producto = p.id
          WHERE c.id_usuario = ? AND c.estado = 'abierto'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$total_general = 0;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Carrito - Vintage Sound Store</title>
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
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link text-warning">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span></li>
        <li class="nav-item"><a class="btn btn-outline-light ms-2 active" href="carrito.php">Carrito</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-5">
  <h1 class="mb-4">Tu Carrito de Compras</h1>

  <?php if (mysqli_num_rows($resultado) > 0): ?>
      <div class="table-responsive bg-white p-4 shadow-sm rounded">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($resultado)): 
                    $total_general += $row['subtotal'];
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="<?= htmlspecialchars($row['imagen_url']) ?>" alt="img" class="me-3 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            <span><?= htmlspecialchars($row['nombre']) ?></span>
                        </div>
                    </td>
                    <td>$<?= number_format($row['precio_unitario'], 2) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td class="fw-bold">$<?= number_format($row['subtotal'], 2) ?></td>
                    <td>
                        <!-- Botón Eliminar -->
                        <form action="acciones_carrito.php" method="POST">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_item" value="<?= $row['id_item'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                    <td colspan="2" class="fw-bold fs-5 text-success">$<?= number_format($total_general, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-end mt-4">
            <a href="catalogo.php" class="btn btn-secondary me-2">Seguir comprando</a>
            
            <!-- Botón Pagar -->
            <form action="acciones_carrito.php" method="POST">
                <input type="hidden" name="accion" value="comprar">
                <button type="submit" class="btn btn-primary btn-lg">Finalizar Compra</button>
            </form>
        </div>
      </div>

  <?php else: ?>
      <div class="alert alert-info text-center py-5">
          <h4>Tu carrito está vacío </h4>
          <p>Ve al catálogo y agrega algunos clásicos.</p>
          <a href="catalogo.php" class="btn btn-primary mt-3">Ir al Catálogo</a>
      </div>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>