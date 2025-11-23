<?php
// Datos de conexión (deben coincidir con docker-compose.yml)
$host = 'db';                 // nombre del servicio del contenedor de MySQL
$user = 'usuario';
$pass = 'somosleones1';
$db   = 'tienda_vintage';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Error de conexión a la base de datos: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');  // opcional pero recomendado
?>
