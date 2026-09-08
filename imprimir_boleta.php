<?php
require_once 'conexion.php';
session_start();

// Capturar el ID del pedido (por defecto 2 para pruebas)
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    // 1. Consultar la cabecera del pedido
    $stmt_ped = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("<br><br><center><h3>No se encontró el registro de la boleta solicitada.</h3></center>");
    }

    // 2. Consultar el desglose de productos comprados en este pedido (JOIN con productos para traer nombres)
    $stmt_detalle = $pdo->prepare("
        SELECT d.*, p.nombre_prod 
        FROM detalle_pedidos d 
        INNER JOIN productos p ON d.id_producto = p.id_producto 
        WHERE d.id_pedido = ?
    ");
    $stmt_detalle->execute([$id_pedido]);
    $items = $stmt_detalle->fetchAll();

    // Lógicas de cálculo para los bloques de solo lectura
    $es_recojo_tienda = (strpos($pedido['tipo_entrega'], 'Recojo') !== false);
    $costo_envoltura = $pedido['envoltura'] == 1 ? 5.00 : 0.00;
    $costo_total_productos = $pedido['total_compra'] - $pedido['costo_envio'] - $costo_envoltura;

} catch (Exception $e) {
    die("Error al consultar los detalles de la boleta en la base de datos.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>BOLETA DE PAGO</title>
   <!-- Vinculamos las hojas de estilos oficiales -->
   <link href="estilos/imprimir_boleta.css" rel="stylesheet">
   <link href="estilos/producto01.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>BOLETA DE PAGO</p>
   </div>
   <br>
   
   <div class="ubicamos">  
      <p>PRODUCTOS COMPRADOS</p>
   </div>
   <br>

   <!-- Formulario estático de solo lectura -->
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
   <div class="informacion">  
      <p>DETALLES DE TRANSACCIÓN</p>
   </div>
   <br>

   <table class="tabla-producto">
      <!-- Fila: Envoltura de regalo -->
      <tr class="fila-gris">
        <td class="col-etiqueta">Agregar envoltura de regalo</td>
        <td class="col-valor">S/. <?php echo number_format($costo_envoltura, 2); ?></td>
        <td class="col-icono">
            <!-- Cuadrado estático con checkmark ✓ si se seleccionó -->
            <div class="cuadrado-interno" style="display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; color: #333;">
                <?php echo ($pedido['envoltura'] == 1) ? '✓' : ''; ?>
            </div>
        </td>
      </tr>
      
      <!-- Fila: Lugar de destino -->
      <tr class="fila-rosada">
         <td class="col-etiqueta">Seleccionar lugar de destino</td>
         <td class="col-valor" colspan="2" style="text-align: left; padding-left: 15px; font-weight: bold;">
             <?php echo htmlspecialchars($pedido['tipo_entrega']); ?>
         </td>
      </tr>
      <tr class="fila-gris">
         <td class="col-etiqueta">Costo de envío</td>
         <td class="col-valor" colspan="2" style="text-align: left; padding-left: 15px;">
             S/. <?php echo number_format($pedido['costo_envio'], 2); ?>
         </td>
      </tr>
      
      <!-- Fila: Recojo en tienda física -->
      <tr class="fila-rosada">
        <td class="col-etiqueta">Recojo en tienda física</td>
        <td class="col-valor">Costo de envío S/. 0.00</td>
        <td class="col-icono">
            <!-- Cuadrado estático con visto si es recojo -->
            <div class="cuadrado-interno" style="display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; color: #333;">
                <?php echo $es_recojo_tienda ? '✓' : ''; ?>
            </div>
        </td>
      </tr>
      
      <!-- Fila: Costo Total -->
      <tr class="fila-gris" style="font-weight: bold;">
        <td class="col-etiqueta">COSTO TOTAL DE LA COMPRA</td>
        <td class="col-valor" colspan="2" style="color: red; font-size: 25px; text-align: left; padding-left: 15px;">S/. <?php echo number_format($pedido['total_compra'], 2); ?></td>
      </tr>
      
      <!-- Fila: Método de pago -->
      <tr class="fila-rosada">
         <td class="col-etiqueta">Seleccionar método de pago</td>
         <td class="col-valor" colspan="2" style="text-align: left; padding-left: 15px; font-weight: bold;">
             <?php echo htmlspecialchars($pedido['metodo_pago']); ?>
         </td>
      </tr>
   </table>
   
   <br><br><br>
   
   <!-- Zona inferior de Botones -->
   <div class="ubicamos_texto">  
      <!-- Botón de Imprimir Boleta en PDF (Abre en pestaña nueva) -->
      <a href="imprimir_boleta_pdf.php?id_pedido=<?php echo $id_pedido; ?>" target="_blank">
         <div class="ubicamos_texto02">
            <p>IMPRIMIR BOLETA</p>
            <p class="p2">Verifique su correo</p>
         </div>
      </a>
      <!-- Enlace dinámico de Devolución para la Cajota -->
      <a href="politicas_devolucion.php" target="_blank" title="Ver políticas de devolución">
         <img src="img/devoluciones-faciles.png" alt="atencion" class="ubicamos_texto03" style="cursor: pointer;">
      </a>
   </div>
   
   <br><br><br><br>


       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>