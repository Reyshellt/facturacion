<?php

$DB_HOST=$_ENV["DB_HOST"];
$DB_USER=$_ENV["DB_USER"];
$DB_PASS=$_ENV["DB_PASS"];
$DB_NAME=$_ENV["DB_NAME"];
$DB_PORT=$_ENV["DB_PORT"];

$conexion = new mysqli("DB_HOST","DB_USER","DB_PASS","DB_NAME","DB_PORT");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
//$conexion->close();
?>