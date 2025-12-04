<?php
session_start();
require 'db.php';

// SEGURIDAD: Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    // Si no es admin, lo mandamos a la tienda normal
    header('Location: index.php');
    exit;
}

// Lógica para BORRAR un producto
if (isset($_GET['borrar'])) {
    $id_borrar = (int)$_GET['borrar'];
    
    $sql = "DELETE FROM productos WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_borrar);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: admin.php?mensaje=borrado');
            exit;
        } else {
            $error = "Error al borrar: Es posible que este producto esté en un pedido histórico.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Obtener la lista de productos con su categoría
$query = "SELECT p.*, c.nombre as nombre_categoria 
          FROM productos p 
          LEFT JOIN categorias c ON p.id_categoria = c.id 
          ORDER BY p.id DESC";
$resultado = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel Admin - Vintage Sound</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Barra de Navegación Admin -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin.php">Panel de Control</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="admin.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_historial.php">Ventas</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-light btn-sm">Ver Tienda</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
      </div>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Inventario</h2>
        <a href="admin_producto.php" class="btn btn-success fw-bold">
            + Nuevo Producto
        </a>
    </div>

    <!-- Mensajes de éxito o error -->
    <?php if(isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Acción realizada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de Productos -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($row['imagen_url']) ?>" 
                                     alt="img" 
                                     class="rounded border bg-white"
                                     style="width: 50px; height: 50px; object-fit: contain;">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($row['nombre']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($row['nombre_categoria'] ?? 'Sin cat.') ?></span>
                            </td>
                            <td>$<?= number_format($row['precio'], 2) ?></td>
                            <td>
                                <?php if($row['stock'] < 3): ?>
                                    <span class="badge bg-danger">Bajo: <?= $row['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $row['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="admin_producto.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="admin.php?borrar=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                   Borrar
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>