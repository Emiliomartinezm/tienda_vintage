<?php
session_start();
require 'db.php';

// SEGURIDAD
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
$titulo = "Agregar Nuevo Producto";

// Valores por defecto (vacíos)
$producto = [
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock' => '',
    'imagen_url' => '',
    'id_categoria' => 1
];

// Si recibimos un ID, cargamos los datos para EDITAR
if ($id) {
    $titulo = "Editar Producto";
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($fila = mysqli_fetch_assoc($res)) {
        $producto = $fila;
    }
    mysqli_stmt_close($stmt);
}

// PROCESAR FORMULARIO (GUARDAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $desc = trim($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $img = trim($_POST['imagen_url']);
    $cat = (int)$_POST['id_categoria'];

    if ($id) {
        // ACTUALIZAR
        $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen_url=?, id_categoria=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdisii", $nombre, $desc, $precio, $stock, $img, $cat, $id);
    } else {
        // INSERTAR NUEVO
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url, id_categoria) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdisi", $nombre, $desc, $precio, $stock, $img, $cat);
    }

    if (mysqli_stmt_execute($stmt)) {
        header('Location: admin.php?mensaje=guardado');
        exit;
    } else {
        $error = "Error al guardar en la base de datos.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= $titulo ?> - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= $titulo ?></h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Precio ($)</label>
                                <input type="number" step="0.01" name="precio" class="form-control" value="<?= $producto['precio'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock (Unidades)</label>
                                <input type="number" name="stock" class="form-control" value="<?= $producto['stock'] ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de la imagen</label>
                            <div class="input-group">
                                <span class="input-group-text">assets/</span>
                                <!-- Limpiamos 'assets/' visualmente para que sea más fácil editar -->
                                <input type="text" name="imagen_url" class="form-control" 
                                       value="<?= htmlspecialchars(str_replace('assets/', '', $producto['imagen_url'])) ?>" 
                                       placeholder="ej: guitarra.jpg" required>
                            </div>
                            
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="id_categoria" class="form-select">
                                <option value="1" <?= $producto['id_categoria'] == 1 ? 'selected' : '' ?>>Vinilos</option>
                                <option value="2" <?= $producto['id_categoria'] == 2 ? 'selected' : '' ?>>Tocadiscos & Audio</option>
                                <option value="3" <?= $producto['id_categoria'] == 3 ? 'selected' : '' ?>>Instrumentos</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Guardar Producto</button>
                            <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pequeño script para asegurar que la ruta siempre se guarde con 'assets/'
    document.querySelector('form').addEventListener('submit', function(e) {
        var inputImg = document.querySelector('input[name="imagen_url"]');
        var valor = inputImg.value.trim();
        
        // Si el usuario no escribió 'assets/', lo agregamos nosotros
        if (valor && !valor.startsWith('assets/')) {
            inputImg.value = 'assets/' + valor;
        }
    });
</script>

</body>
</html>

