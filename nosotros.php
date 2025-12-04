<?php
session_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nosotros - Vintage Sound Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      .hero-section {
          background: url('https://images.unsplash.com/photo-1461360370896-922624d12aa1?q=80&w=2074&auto=format&fit=crop') no-repeat center center;
          background-size: cover;
          height: 400px;
          position: relative;
      }
      .overlay {
          background-color: rgba(0, 0, 0, 0.6);
          position: absolute;
          top: 0; left: 0; width: 100%; height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
      }
  </style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link active" href="nosotros.php">Nosotros</a></li>
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

<!-- HERO IMAGE -->
<div class="hero-section">
    <div class="overlay">
        <h1 class="text-white display-3 fw-bold">Nuestra Historia</h1>
    </div>
</div>

<main class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="mb-4">Más que una tienda, un viaje en el tiempo</h2>
            <p class="lead text-muted">Fundada en 2025, Vintage Sound Store nació de la pasión por lo analógico en un mundo digital.</p>
            <p>
                Creemos que la música no se escucha igual cuando haces clic en una pantalla que cuando sacas un vinilo de su funda, observas el arte de la portada y dejas caer la aguja. 
                Nuestra misión es rescatar esos tesoros sonoros y ponerlos en manos de quienes aprecian la calidez del sonido auténtico.
            </p>
            <p>
                Desde guitarras con historia hasta cassettes que definieron una era, cada artículo en nuestro catálogo ha sido seleccionado cuidadosamente para garantizar calidad y nostalgia.
            </p>
        </div>
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1542208998-f6dbbb27a72f?q=80&w=2070&auto=format&fit=crop" class="img-fluid rounded shadow" alt="Tienda de discos">
        </div>
    </div>

    <div class="row text-center mt-5">
        <div class="col-md-4">
            <div class="p-4 bg-white shadow-sm rounded">
                <h3 class="h5">Calidad Garantizada</h3>
                <p class="small">Revisamos cada vinilo y equipo para asegurar su funcionamiento.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white shadow-sm rounded">
                <h3 class="h5">Envíos Seguros</h3>
                <p class="small">Empaquetamos con protección especial para evitar daños.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white shadow-sm rounded">
                <h3 class="h5">Pasión Musical</h3>
                <p class="small">Asesoramiento experto para encontrar el sonido que buscas.</p>
            </div>
        </div>
    </div>
</main>

<footer class="py-4 bg-dark text-light">
    <div class="container text-center">
    <small>&copy; <?php echo date('Y'); ?> Vintage Sound Store</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>