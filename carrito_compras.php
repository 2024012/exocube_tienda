<?php
require_once 'conexion.php';
session_start();

// 1. Lógica para ELIMINAR un producto si el usuario presiona "Quitar" [X]
if (isset($_GET['accion']) && $_GET['accion'] == 'quitar' && isset($_GET['id'])) {
    $id_quitar = (int)$_GET['id'];
    unset($_SESSION['carrito'][$id_quitar]);
    
    header("Location: carrito_compras.php");
    exit;
}

// 2. Lógica para AGREGAR un producto enviado desde la ficha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion_carrito']) && $_POST['accion_carrito'] == 'agregar') {
    $id_p = (int)$_POST['id_producto'];
    $cant = (int)$_POST['cantidad'];
    
    if ($cant > 0) {
        $_SESSION['carrito'][$id_p] = $cant;
    }
}

// 3. Obtener detalles de los productos del carrito
$productos_carrito = [];
$total_productos = 0.00;

if (!empty($_SESSION['carrito'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['carrito']), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['carrito']));
    $resultados = $stmt->fetchAll();
    
    foreach ($resultados as $row) {
      $id_actual = $row['id_producto'];
      $cantidad_actual = $_SESSION['carrito'][$id_actual];
      
      // Cálculo dinámico del precio de oferta si aplica descuento
      $precio_unidad = $row['precio'];
      if ($row['descuento'] > 0) {
          $precio_unidad = $row['precio'] * (1 - ($row['descuento'] / 100));
      }
      
      $subtotal = $precio_unidad * $cantidad_actual;
      
      $productos_carrito[] = [
          'id' => $id_actual,
          'nombre' => $row['nombre_prod'],
          'precio' => $precio_unidad, // Guardar el precio descontado
          'cantidad' => $cantidad_actual,
          'subtotal' => $subtotal
      ];
      $total_productos += $subtotal;
  }
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>CARRITO DE COMPRAS</title>
   <!-- Vinculamos la hoja de estilos exclusiva del carrito de compras -->
   <link href="estilos/carrito_compras.css" rel="stylesheet">
   <!-- Vinculamos también los estilos de la tabla de productos para heredar las filas rosas y grises -->
   <link href="estilos/producto01.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
      <script>
         function actualizarTotales() {
            let totalProds = <?php echo $total_productos; ?>;
            let costoEnvio = 10.00; 
            let costoEnvoltura = 5.00; 
            
            let recojoTienda = document.getElementById('recojo_tienda').checked;
            if (recojoTienda) {
               costoEnvio = 0.00;
               document.getElementById('envio_valor').innerText = "S/. 0.00";
               
               // Ocultar e inhabilitar dirección exacta
               document.getElementById('fila_direccion').style.opacity = "0.5";
               document.getElementById('direccion_input').disabled = true;
               document.getElementById('direccion_input').required = false;
               document.getElementById('direccion_input').value = "";
               document.getElementById('destino_select').disabled = true;
            } else {
               document.getElementById('envio_valor').innerText = "S/. " + costoEnvio.toFixed(2);
               
               // Habilitar dirección exacta obligatoria
               document.getElementById('fila_direccion').style.opacity = "1";
               document.getElementById('direccion_input').disabled = false;
               document.getElementById('direccion_input').required = true;
               document.getElementById('destino_select').disabled = false;
            }
            
            let requiereEnvoltura = document.getElementById('envoltura').checked;
            if (requiereEnvoltura) {
               document.getElementById('envoltura_valor').innerText = "S/. " + costoEnvoltura.toFixed(2);
            } else {
               costoEnvoltura = 0.00;
               document.getElementById('envoltura_valor').innerText = "S/. 0.00";
            }
            
            let costoTotal = totalProds + costoEnvio + costoEnvoltura;
            document.getElementById('total_compra_valor').innerText = "S/. " + costoTotal.toFixed(2);
         }
      </script>
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>CARRITO DE COMPRAS</p>
   </div>
   <br>
   
   <div class="ubicamos">  
      <p>PRODUCTOS SELECCIONADOS</p>
   </div>
   <br>

   <!-- Formulario principal con ID asignado para control por Javascript -->
   <form id="form-carrito" action="procesar_pedido.php" method="POST" target="_blank">
      <table class="tabla-producto">
         <tr class="fila-gris" style="font-weight: bold; text-align: center;">
            <td style="width: 15%;">Cantidad</td>
            <td style="width: 60%;">Nombre de Producto</td>
            <td style="width: 100%;">Subtotal</td>
            <td style="width: 15%;">Acción</td> 
         </tr>
         
         <?php if (!empty($productos_carrito)): ?>
            <?php foreach ($productos_carrito as $item): ?>
               <tr class="fila-rosada">
                  <td style="text-align: center; font-weight: bold;"><?php echo str_pad($item['cantidad'], 2, "0", STR_PAD_LEFT); ?></td>
                  <td style="text-align: left; padding-left: 15px;"><?php echo htmlspecialchars($item['nombre']); ?></td>
                  <td style="text-align: center; font-weight: bold;">S/. <?php echo number_format($item['subtotal'], 2); ?></td>
                  <td style="text-align: center;">
                      <a href="carrito_compras.php?accion=quitar&id=<?php echo $item['id']; ?>" style="color: red; font-weight: bold; text-decoration: none;">[Quitar]</a>
                  </td>
               </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <tr class="fila-rosada">
               <td colspan="4" style="text-align: center; color: #666;">Tu carrito de compras está vacío.</td>
            </tr>
         <?php endif; ?>
         
         <tr class="fila-gris" style="font-weight: bold;">
            <td colspan="2" style="text-align: left; padding-left: 15px;">Costo total de los productos</td>
            <td colspan="2" style="text-align: center; color: #cc0000;">S/. <?php echo number_format($total_productos, 2); ?></td>
         </tr>
      </table>
      
      <br><br>
      <div class="informacion">  
         <p>OPCIONES DE ENTREGA Y PAGO</p>
      </div>
      <br>

      <table class="tabla-producto">
         <tr class="fila-gris">
           <td class="col-etiqueta">Agregar envoltura de regalo</td>
           <td class="col-valor" id="envoltura_valor">S/. 0.00</td>
           <td class="col-icono">
               <input type="checkbox" name="envoltura" id="envoltura" value="1" onchange="actualizarTotales()" style="width:40px; height:40px; cursor:pointer;">
           </td>
         </tr>
         
      </table>
      <br><br>
      <table class="tabla-producto">
         <tr class="fila-rosada">
            <td class="col-etiqueta">Seleccionar lugar de destino</td>
            <td class="col-valor" colspan="2">
               <select name="destino" style="width: 90%; padding: 5px; font-family: inherit; font-size: 20px;">
                  <option value="Tacna Centro">Tacna Centro (Envío regular)</option>
                  <option value="Cono Sur">Cono Sur (Envío regular)</option>
                  <option value="Cono Norte">Cono Norte (Envío regular)</option>
               </select>
            </td>
         </tr>
         <tr class="fila-gris">
           <td class="col-etiqueta">Costo de envío</td>
           <td class="col-valor" id="envio_valor" colspan="2">S/. 10.00</td>
         </tr>
         
      </table>
      <br><br>
      <table class="tabla-producto">
         
         <tr class="fila-rosada">
           <td class="col-etiqueta">Recojo en tienda física</td>
           <td class="col-valor">Costo de envío S/. 0.00</td>
           <td class="col-icono">
               <input type="checkbox" name="recojo_tienda" id="recojo_tienda" value="1" onchange="actualizarTotales()" style="width:40px; height:40px; cursor:pointer;">
           </td>
         </tr>
         
         
      </table>
      <br><br>
      <table class="tabla-producto">
         
         
         <tr class="fila-gris" style="font-weight: bold;">
           <td class="col-etiqueta">COSTO TOTAL DE LA COMPRA</td>
           <td class="col-valor" id="total_compra_valor" colspan="2" style="color: red; font-size: 25px;">S/. <?php echo number_format($total_productos + 10.00, 2); ?></td>
         </tr>
         
         
      </table>
      <br><br>
      <table class="tabla-producto">
         
         <tr class="fila-rosada">
            <td class="col-etiqueta">Seleccionar método de pago</td>
            <td class="col-valor" colspan="2">
               <select name="metodo_pago" style="width: 90%; padding: 5px; font-family: inherit; font-size: 20px;">
                  <option value="Yape/Plin">Billetera Digital (Yape / Plin)</option>
                  <option value="Transferencia">Transferencia Bancaria Directa</option>
                  <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
               </select>
            </td>
         </tr>
         
      </table>
      <br><br>
      
      <table class="tabla-producto">
      
         <!-- NUEVA FILA GRIS: Dirección exacta de entrega -->
         <tr class="fila-gris" id="fila_direccion">
           <td class="col-etiqueta">Dirección exacta de entrega (Calle, Nro)</td>
           <td class="col-valor" colspan="2">
               <input type="text" name="direccion_entrega" id="direccion_input" required placeholder="Ej: Calle San Martín 450, Dpto 201..." style="width: 90%; padding: 5px; font-family: inherit; font-size: 14px; border: 2px solid black;">
           </td>
         </tr>
      </table>
      <br><br>
      <div class="informacion">  
         <p>DATOS DE DESPACHO Y CONTACTO</p>
      </div>
      <br>
      <table class="tabla-producto">
         <!-- NUEVA FILA GRIS: Nombre y Apellido del Cliente -->
         <tr class="fila-gris">
           <td class="col-etiqueta">Nombre y Apellido del Cliente</td>
           <td class="col-valor" colspan="2">
               <input type="text" name="cliente_nombre" required placeholder="Escriba su nombre y apellido completo..." style="width: 90%; padding: 5px; font-family: inherit; font-size: 14px; border: 2px solid black;">
           </td>
         </tr>
      </table>
      <br><br>
      
      <table class="tabla-producto">
         
         <!-- NUEVA FILA ROSADA: Celular de contacto -->
         <tr class="fila-rosada">
           <td class="col-etiqueta">Número de Celular (Para el courier)</td>
           <td class="col-valor" colspan="2">
               <input type="text" name="cliente_celular" required placeholder="Escriba su número de celular aquí..." style="width: 90%; padding: 5px; font-family: inherit; font-size: 14px; border: 2px solid black;">
           </td>
         </tr>
         
      </table>
      <br><br>
      <table class="tabla-producto">
         <!-- Fila: Correo de contacto -->
         <tr class="fila-gris">
           <td class="col-etiqueta">Correo electrónico de contacto</td>
           <td class="col-valor" colspan="2">
               <input type="email" name="cliente_correo" required placeholder="Escriba su correo de contacto..." style="width: 90%; padding: 5px; font-family: inherit; font-size: 14px; border: 2px solid black;">
           </td>
         </tr>
         
      </table>
     
      <br><br><br>
      
      <!-- Campo oculto para validar la confirmación en el procesador PHP -->
      <input type="hidden" name="confirmar_compra" value="1">

      <!-- Mantenemos la estructura HTML original de tu plantilla de forma exacta -->
      <div class="ubicamos_texto">  
         <!-- Al hacer clic aquí, se envía de forma segura el formulario mediante JS -->
         <div class="ubicamos_texto02" onclick="document.getElementById('form-carrito').submit();" style="cursor: pointer;">
            <p>CONFIRMAR COMPRA</p>
            <p class="p2">No se aceptan devoluciones</p>
         </div>
         <!-- Al hacer clic aquí, abre las políticas de devolución en pestaña nueva -->
         <img src="img/devoluciones-faciles.png" alt="atencion" class="ubicamos_texto03" onclick="window.open('politicas_devolucion.php', '_blank');" style="cursor: pointer;">
      </div>
   </form>
   
   <br><br><br><br>

     <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
     <?php include 'footer.php'; ?>
   
   <script>
      actualizarTotales();
   </script>

</body>
</html>