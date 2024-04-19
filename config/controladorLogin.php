<?php

session_start();

if (isset($_POST["btnIngresar"])) {
    
    if (!empty($_POST["nombre_usuario"]) and !empty($_POST["contrasena"])) {
        $nombre_usuario = $_POST["nombre_usuario"];
        $contrasena = $_POST["contrasena"];

        include_once 'config/configx.php'; 

        $sql = $conexion->query("SELECT * FROM usuarios WHERE nombre_usuario = '$nombre_usuario' AND contrasena = '$contrasena'");
        
        if ($datos = $sql->fetch_object()) {

            $_SESSION["id"]=$datos->id;
            $_SESSION["nombre_usuario"]=$datos->nombre;

            header("Location: factura.php");
            exit();

        } else {
            echo "<div class='alert alert-danger'> Acceso denegado </div>";
        }

    } else {
        echo "<div class='alert alert-danger'> Campos vacíos </div>";
    }
}
?>
