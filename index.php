<?php

session_start(); 
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Vintage Sound Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!--Bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-light">

    <!--  MENÚ DE NAVEGACIÓN (La barra negra de arriba) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <!-- El Logo / Nombre de la tienda -->
        <a class="navbar-brand" href="index.php">Vintage Sound</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
          <!-- Enlaces de la IZQUIERDA -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="catalogo.php">Catálogo</a></li>
            <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
            <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
          </ul>
          
          <!-- MENÚ INTELIGENTE (LADO DERECHO) -->
          <!-- la página decide qué mostrarte según quién eres -->
          <ul class="navbar-nav ms-auto">
            
            <?php 
            // PREGUNTA 1: ¿Hay alguien conectado? (¿Existe la variable usuario_id?)
            if (isset($_SESSION['usuario_id'])): 
            ?>
                
                <!-- CASO A: SÍ, HAY ALGUIEN CONECTADO -->

                <?php 
                // PREGUNTA 2: ¿Esa persona es el JEFE (admin)?
                if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): 
                ?>
                    <!-- Si es el jefe, le mostramos el botón secreto del Panel -->
                    <li class="nav-item">
                        <a class="nav-link text-info fw-bold" href="admin.php">Panel Admin</a>
                    </li>
                <?php endif; ?>

                <!-- Muestra el nombre de la persona ("Hola, Emilio") -->
                <li class="nav-item">
                    <span class="nav-link text-warning">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                </li>
                
                <!-- Botón para Salir  -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>

            <?php else: ?>
                
                <!-- CASO B: NO HAY NADIE CONECTADO -->
                <!-- Entonces mostramos el botón para entrar -->
                <li class="nav-item"><a class="nav-link" href="login.php">Iniciar sesión</a></li>
            
            <?php endif; ?>

            <!-- El botón del carrito siempre se muestra, no importa quién seas -->
            <li class="nav-item">
              <a class="btn btn-outline-light ms-2" href="carrito.php">
                Carrito
              </a>
            </li>
          </ul>

        </div>
      </div>
    </nav>

    <!-- PASO 4: LA PORTADA  -->
    <header class="py-5 bg-dark text-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h1 class="display-4 fw-bold">Vintage Sound Store</h1>
            <p class="lead">Vinilos, cassettes, tocadiscos y guitarras con alma clásica.</p>
            <!-- Botón azul que te lleva a ver los productos -->
            <a href="catalogo.php" class="btn btn-primary btn-lg">Ver catálogo</a>
          </div>
        </div>
      </div>
    </header>

    <!-- PASO 5: Lo que ofrecemos -->
    <main class="py-5">
      <div class="container">
        <h2 class="mb-4">Lo que encontrarás aquí</h2>
        <div class="row g-4">
          
          <!-- Tarjeta 1 -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">Vinilos & Cassettes</h5>
                <p class="card-text">Ediciones clásicas y especiales para coleccionistas de música.</p>
              </div>
            </div>
          </div>

          <!-- Tarjeta 2 -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">Tocadiscos</h5>
                <p class="card-text">Reproductores con estilo retro para sacar el mejor sonido de tus discos.</p>
              </div>
            </div>
          </div>

          <!-- Tarjeta 3 -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">Guitarras</h5>
                <p class="card-text">Modelos eléctricos y acústicos con vibra vintage.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>

    <!-- PASO 6: EL PIE DE PÁGINA (Footer) -->
    <footer class="py-4 bg-dark text-light">
      <div class="container text-center">
        <small>&copy; <?php echo date('Y'); ?> Vintage Sound Store</small>
      </div>
    </footer>

    <!-- Scripts necesarios para que los menús desplegables funcionen en celular -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>