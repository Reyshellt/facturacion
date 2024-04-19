<?php

    session_start();

    if (empty($_SESSION["id"])) {
        header("Location: index.php");
    }

    function mostrarFacturas() {

    include_once 'config/configx.php';

    // Consulta SQL para obtener las facturas
    $query = "SELECT id, fecha, nombre_cliente, total FROM facturas";
    $resultado = $conexion->query($query);

    // Verificar si la consulta se ejecutó correctamente
    if ($resultado) {
        // Mostrar las facturas
        while ($fila = $resultado->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $fila['id'] . '</td>';
            echo '<td>' . $fila['fecha'] . '</td>';
            echo '<td>' . $fila['nombre_cliente'] . '</td>';
            echo '<td>' . $fila['total'] . '</td>';
            echo "<td><a href='imprimir.php?id={$fila['id']}' class='btn btn-primary'> Imprimir </a></td>";
            echo '</tr>';
        }
    } else {
        // Imprimir un mensaje de error si la consulta falló
        echo "Error al ejecutar la consulta: " . $conexion->error;
    }

    // Cerrar la conexión
    $conexion->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
    <div class="container">
        <nav class=" navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/factura.php">Facturacion</a>
                <a class="btn btn-primary" href="/config/cerrarSession.php" role="button">Salir</a>
            
            </div>
        </nav>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin: 20px;">
            <a href="facturar.php" class="btn btn-primary">
                Nueva Factura
            </a>
        </div>

        <div>
            <h3>Facturas realizadas</h3>
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php mostrarFacturas(); ?>
                </tbody>
            </table>
        </div>
    </div>



</body>
</html>