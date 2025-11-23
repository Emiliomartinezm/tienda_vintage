<?php
echo "<h1>Prueba de Diagnóstico</h1>";

// 1. Verificar versión de PHP
echo "Versión PHP: " . phpversion() . "<br>";

// 2. Preguntar directamente a PHP si conoce la función
if (function_exists('mysqli_connect')) {
    echo "<h2 style='color:green'>¡EXITO! La función mysqli_connect SI existe.</h2>";
    echo "El problema NO es Docker, es tu código de login/db.php o la ruta.";
} else {
    echo "<h2 style='color:red'>ERROR FATAL: La función NO existe.</h2>";
    echo "El problema sigue siendo Docker/Configuración.";
}
?>