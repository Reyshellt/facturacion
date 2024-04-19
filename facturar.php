<?php

session_start();

    if (empty($_SESSION["id"])) {
        header("Location: /inicio.php");
    }

include_once '/app/config/configx.php';

if($_POST){
    
    $fecha = $_POST['fecha'];
    $codigoCliente = $_POST['codigoCliente'];
    $nombreCliente = $_POST['nombreCliente'];
    $total = $_POST['total'];
    $comentario = $_POST['comentario'];

    // Insertar factura en la base de datos
    $query = "INSERT INTO facturas (fecha, codigo_cliente, nombre_cliente, total, comentario) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("sssss", $fecha, $codigoCliente, $nombreCliente, $total, $comentario);
    $stmt->execute();

    // Obtener el ID de la factura insertada
    $idFactura = $stmt->insert_id;

    // Insertar detalles de factura en la base de datos
    $nombres = $_POST['nombre'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio'];
    foreach ($nombres as $key => $nombre) {
        $cantidad = $cantidades[$key];
        $precio = $precios[$key];
        $query = "INSERT INTO detalles_factura (id_factura, nombre_producto, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("issd", $idFactura, $nombre, $cantidad, $precio);
        $stmt->execute();
    }

    //echo "Factura guardada correctamente.";
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <nav class=" navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/factura.php">Facturacion</a>
                <a class="btn btn-primary" href="/config/cerrarSession.php" role="button">Salir</a>
            
            </div>
        </nav>

        <div style="margin: 20px;">
            <h3>Facturar </h3>
        </div>
        
        <form action="facturar.php" method="post">
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label for="codigoCliente" class="form-label">Codigo del cliente</label>
                <input type="text" class="form-control" id="codigoCliente" name="codigoCliente">
            </div>
            <div class="mb-3">
                <label for="nombreCliente" class="form-label">Nombre del cliente</label>
                <input type="text" class="form-control" id="nombreCliente" name="nombreCliente">
            </div>

            <div>
                <table class="table table-border">
                    <thead>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                        <td>
                            <button type="button" class="btn-primary" onclick="agregarfila()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </thead>
                    <tbody id="tbDetalles">
                    
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <label for="total" class="form-label">Total a Pagar</label>
                <input type="text" readonly class="form-control" id="total" name="total">
            </div>

            <div class="mb-3">
                <label for="comentario" class="form-label">Comentario</label>
                <textarea class="form-control" id="comentario" name="comentario"> </textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn-primary">Facturar</button>
            </div>
        </form>
    </div>

</body>

<script>

    function calcularTotal(){
        let total = 0;
        let cantidad = document.getElementsByName('cantidad[]');
        let precio = document.getElementsByName('precio[]');
        let totalInput = document.getElementsByName('total[]');
        for (let i = 0; i < cantidad.length; i++){
            subtotal= (cantidad[i].value * precio[i].value).toFixed(2);
            totalInput[i].value= subtotal;
            total += parseFloat(subtotal);
        }
        document.getElementById('total').value = total.toFixed(2);
    }

    function agregarfila(){
        let tbody= document.getElementById('tbDetalles');
        let tr= document.createElement('tr');
        tr.innerHTML=
            `<td>
                <input type="text" class="form-control" name="nombre[] ">
            </td>
            <td>
                <input type="number" class="form-control" name="cantidad[]" onkeyup="calcularTotal()">
            </td>
            <td>
                <input type="number" class="form-control" name="precio[]" onkeyup="calcularTotal()">
            </td>
            <td>
                <input type="number" readonly class="form-control" name="total[]" >
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="eliminarFila(this)">
                <i class="fas fa-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr); 
    }

    function eliminarFila(btn){
        fila = btn.parentNode.parentNode;
        Swal.fire({
            title:'¿Estas seguro?',
            text:'NO PODRAS REVERTIR ESTO!!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor:'#3085d6',
            cancelButtonColor:'#d33',
            confirmButtonText:'Si, eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                fila.parentNode.removeChild(fila);
                Swal.fire(
                    'Eliminado',
                    'El registro ha sido eliminado.',
                    'success'
                )
            }
        })
    }
</script>

</html>