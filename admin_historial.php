<?php
session_start();
require 'db.php';

// SEGURIDAD
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Consultar todos los pedidos ordenados por fecha (más reciente primero)
$query = "SELECT p.id, p.total, p.fecha_creacion, p.estado, u.nombre as cliente, u.email 
          FROM pedidos p 
          JOIN usuarios u ON p.id_usuario = u.id 
          ORDER BY p.fecha_creacion DESC";
$resultado = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Historial de Ventas - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin.php">Panel Admin</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="admin.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_historial.php">Ventas</a></li>
      </ul>
      <a href="index.php" class="btn btn-outline-light btn-sm">Volver a Tienda</a>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="mb-4 text-primary">Historial de Ventas</h2>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th># Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td class="fw-bold">#<?= $row['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
                                <td><?= htmlspecialchars($row['cliente']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td class="fw-bold text-success">$<?= number_format($row['total'], 2) ?></td>
                                <td>
                                    <?php if($row['estado'] == 'pagado'): ?>
                                        <span class="badge bg-success">PAGADO</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?= strtoupper($row['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <h4>No hay ventas registradas aún.</h4>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

