<?php

session_start();

if (empty($_SESSION["id"])) {
    header("Location: inicio.php");
}
// Obtener el ID de la factura de la URL
$id = (isset($_GET['id'])) ? $_GET['id'] : '0';

include_once 'config/configx.php';

try {
    // Consulta SQL para obtener los datos de la factura
    $query_factura = "SELECT * FROM facturas WHERE id = ?";
    $stmt_factura = $conexion->prepare($query_factura);
    $stmt_factura->bind_param("i", $id);
    $stmt_factura->execute();
    $resultado_factura = $stmt_factura->get_result();

    // Verificar si se encontró la factura
    if ($resultado_factura->num_rows > 0) {
        // Obtener los datos de la factura
        $datos_factura = $resultado_factura->fetch_assoc();

        // Consulta SQL para obtener los detalles de la factura
        $query_detalles = "SELECT nombre_producto, cantidad, precio FROM detalles_factura WHERE id_factura = ?";
        $stmt_detalles = $conexion->prepare($query_detalles);
        $stmt_detalles->bind_param("i", $id);
        $stmt_detalles->execute();
        $resultado_detalles = $stmt_detalles->get_result();

        // Verificar si se encontraron detalles de la factura
        if ($resultado_detalles->num_rows > 0) {
            // Almacenar los detalles de la factura en un array
            $detalles = array();
            while ($row = $resultado_detalles->fetch_assoc()) {
                $detalles[] = $row;
            }

            // Crear el PDF y generar la factura con los datos recuperados...
            require('libreria/fpdf.php');

            class PDF extends FPDF
            {
                function Header()
                {
                    // Logo
                    //$this->Image('logo.png',10,6,30);
                    $this->SetFont('Arial','B',15);
                    $this->Cell(80);
                    $this->Cell(30,10,'Factura',0,0,'C');
                    $this->Ln(20);
                }

                
                function Footer()
                {
                    
                    $this->SetY(-15);
                    
                    $this->SetFont('Arial','I',8);
                    
                    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
                }

                // Crear la factura con los datos
                function CreateInvoice($datos_factura, $detalles)
                {
                    $this->SetFont('Arial','B',12);
                    $this->Cell(40,10,'Fecha: '.$datos_factura['fecha']);
                    $this->Ln();

                    $this->Cell(40,10,'Codigo de Cliente: '.$datos_factura['codigo_cliente']);
                    $this->Ln();

                    $this->Cell(40,10,'Nombre del Cliente: '.$datos_factura['nombre_cliente']);
                    $this->Ln();

                    $this->Cell(40,10,'Detalles:');
                    $this->Ln();

                    // Datos de la factura (nombre, cantidad, precio)
                    $this->SetFont('Arial','',10);
                    $this->Cell(60,10,'Nombre',1,0,'C');
                    $this->Cell(30,10,'Cantidad',1,0,'C');
                    $this->Cell(30,10,'Precio',1,0,'C');
                    $this->Cell(40,10,'Total',1,0,'C');
                    $this->Ln();

                    // Calcular el total de la factura
                    $totalFactura = 0;

                    // Recorrer los detalles de la factura
                    foreach ($detalles as $detalle) {
                        $this->Cell(60,10,$detalle['nombre_producto'],1,0,'C');
                        $this->Cell(30,10,$detalle['cantidad'],1,0,'C');
                        $this->Cell(30,10,$detalle['precio'],1,0,'C');
                        $total = $detalle['cantidad'] * $detalle['precio'];
                        $this->Cell(40,10,$total,1,0,'C');
                        $this->Ln();
                        $totalFactura += $total;
                    }

                    $this->Cell(120,10,'Total: '.$totalFactura,1,0,'R');
                }
            }

            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->CreateInvoice($datos_factura, $detalles);
            ob_clean(); 
            $pdf->Output();
        } else {
            throw new Exception("No se encontraron detalles de la factura.");
        }
    } else {
        throw new Exception("Factura no encontrada.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$stmt_factura->close();
$stmt_detalles->close();
$conexion->close();
?>
