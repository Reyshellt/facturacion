<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'facturacion_db');
define('DB_PORT', '3306');

$conexion = new mysqli("DB_HOST","DB_USER","DB_PASS","DB_NAME","DB_PORT");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
//$conexion->close();
?>