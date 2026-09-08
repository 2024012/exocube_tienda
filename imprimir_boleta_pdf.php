<?php
require_once 'conexion.php';
session_start();

$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    $stmt_ped = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("Boleta no encontrada.");
    }

    $stmt_detalle = $pdo->prepare("
        SELECT d.*, p.nombre_prod, p.precio as precio_orig 
        FROM detalle_pedidos d 
        INNER JOIN productos p ON d.id_producto = p.id_producto 
        WHERE d.id_pedido = ?
    ");
    $stmt_detalle->execute([$id_pedido]);
    $items = $stmt_detalle->fetchAll();

    $es_recojo_tienda = (strpos($pedido['tipo_entrega'], 'Recojo') !== false);
    $costo_envoltura = $pedido['envoltura'] == 1 ? 5.00 : 0.00;
    $costo_total_productos = $pedido['total_compra'] - $pedido['costo_envio'] - $costo_envoltura;

} catch (Exception $e) {
    die("Error al generar la boleta.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>Boleta_Compra_<?php echo $id_pedido; ?>_EXOCUBE</title>
   <!-- Cargamos tus estilos para mantener la tipografía y bordes originales -->
   <link href="estilos/producto01.css" rel="stylesheet">
   <style>
      body {
         background-color: #ffffff; /* Fondo blanco óptimo para impresión */
         padding: 40px;
         font-family: 'Alfaqix', sans-serif;
      }
      .somos p {
         color: #000000 !important;
         -webkit-text-stroke: 0px !important;
         font-size: 50px !important;
         margin-bottom: 20px;
      }
      .tabla-producto {
         border: 3px solid #333 !important;
      }
      .tabla-producto td {
         border: 2px solid #333 !important;
      }
      .fila-gris {
         background-color: #f2f2f2 !important;
      }
      .fila-rosada {
         background-color: #fff0f2 !important;
      }
      /* Ocultar botones interactivos al momento de imprimir */
      @media print {
         .no-imprimir {
            display: none !important;
         }
      }
   </style>
</head>
<!-- Al cargar la pestaña, gatilla automáticamente el diálogo de impresión para descarga de PDF -->
<body onload="window.print();">

   <div class="somos" style="text-align: center;">  
      <p>BOLETA DE PAGO OFICIAL</p><br>
      <h3 style="font-size: 40px; margin: 0; line-height: 1.6;">EXOCUBE TIENDA S.A.</h3><br><br>
      <p style="font-size: 14px; color: #777; margin-top: 5px;line-height: 1.6;">Fecha de emisión: <?php echo date("d/m/Y H:i:s", strtotime($pedido['fecha_pedido'])); ?></p>
   </div>
   
   
   <table class="tabla-producto">
      <tr class="fila-gris" style="font-weight: bold; text-align: center;">
         <td style="width: 20%;">Cantidad</td>
         <td style="width: 50%;">Nombre de Producto</td>
         <td style="width: 30%;">Subtotal</td>
      </tr>
      
      <?php foreach ($items as $item): ?>
         <tr class="fila-rosada">
            <td style="text-align: center; font-weight: bold;"><?php echo str_pad($item['cantidad'], 2, "0", STR_PAD_LEFT); ?></td>
            <td style="text-align: left; padding-left: 15px;"><?php echo htmlspecialchars($item['nombre_prod']); ?></td>
            <td style="text-align: center; font-weight: bold;">S/. <?php echo number_format($item['subtotal'], 2); ?></td>
         </tr>
      <?php endforeach; ?>
      
      <tr class="fila-gris" style="font-weight: bold;">
         <td colspan="2" style="text-align: left; padding-left: 15px;">Costo total de los productos</td>
         <td style="text-align: center; color: #cc0000;">S/. <?php echo number_format($costo_total_productos, 2); ?></td>
      </tr>
   </table>
   
   <br><br>

   <table class="tabla-producto">
      <tr class="fila-gris">
        <td class="col-etiqueta">Envoltura de regalo</td>
        <td class="col-valor" colspan="2">S/. <?php echo number_format($costo_envoltura, 2); ?> <?php echo ($pedido['envoltura'] == 1) ? '(Sí)' : '(No)'; ?></td>
      </tr>
      <tr class="fila-rosada">
         <td class="col-etiqueta">Lugar de destino</td>
         <td class="col-valor" colspan="2" style="font-weight: bold; text-align: left; padding-left: 15px;">
             <?php echo htmlspecialchars($pedido['tipo_entrega']); ?>
         </td>
      </tr>
      <tr class="fila-gris">
         <td class="col-etiqueta">Costo de envío</td>
         <td class="col-valor" colspan="2" style="text-align: left; padding-left: 15px;">
             S/. <?php echo number_format($pedido['costo_envio'], 2); ?>
         </td>
      </tr>
      <tr class="fila-gris" style="font-weight: bold;">
        <td class="col-etiqueta" style="font-size: 18px;">COSTO TOTAL PAGADO</td>
        <td class="col-valor" colspan="2" style="color: red; font-size: 20px; text-align: left; padding-left: 15px;">S/. <?php echo number_format($pedido['total_compra'], 2); ?></td>
      </tr>
      <tr class="fila-rosada">
         <td class="col-etiqueta">Método de pago utilizado</td>
         <td class="col-valor" colspan="2" style="text-align: left; padding-left: 15px; font-weight: bold;">
             <?php echo htmlspecialchars($pedido['metodo_pago']); ?>
         </td>
      </tr>
   </table>
   
   <br><br><br>
   
   <div style="text-align: center;" class="no-imprimir">
       <button onclick="window.close();" style="font-family: inherit; font-size: 16px; padding: 10px 30px; cursor: pointer; border: 2px solid black; background-color: #f2f2f2; font-weight: bold;">Cerrar Vista de Impresión</button>
   </div>
   
</body>
</html>