<?php
require_once 'conexion.php';
session_start();

$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    // Consultar los datos de la boleta de compra
    $stmt_ped = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("Boleta no encontrada.");
    }

    // Consultar la dirección y el mapa de la sucursal del primer local de tu base de datos
    $stmt_local = $pdo->query("SELECT * FROM locales_tienda ORDER BY id_local ASC LIMIT 1");
    $local = $stmt_local->fetch();

} catch (Exception $e) {
    die("Error al consultar el mapa de despacho.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>LUGAR DE ENTREGA</title>
   <!-- Reutilizamos tu hoja de estilos de mapa de ubicación de forma limpia -->
   <link href="estilos/mapa_ubicacion.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>LUGAR DE ENTREGA</p>
   </div>
   <br><br><br><br><br><br>
   
   <!-- Cuadro de texto intermedio para el cliente -->
   <div class="experiencia_texto">  
      <p>Su Pedido nro. <?php echo $id_pedido; ?> está siendo coordinado para entrega en la siguiente dirección autorizada de despacho.</p>
   </div>
   <br><br><br><br><br>
  
   <!-- ... (Sección del cuadro de dirección de lugar_entrega.php) ... -->
   <div class="ubicamos_texto">  
      <div class="ubicamos_texto02">
         <p style="font-weight: bold; margin-bottom: 10px; font-size: 20px;">DIRECCIÓN ASOCIADA:</p>
         <!-- Mostramos el distrito -->
         <p style="font-weight: bold; margin: 0;"><?php echo htmlspecialchars($pedido['tipo_entrega']); ?></p>
         <!-- CORRECCIÓN: Mostramos la calle y número exacto -->
         <p style="font-family: sans-serif; font-size: 16px; color: #444; margin-top: 5px;">
             <?php echo !empty($pedido['direccion_entrega']) ? htmlspecialchars($pedido['direccion_entrega']) : 'Recojo en nuestro local de Tacna.'; ?>
         </p>
      </div>
      <!-- Mapa interactivo de la sucursal del local donde se despacha el producto -->
      <?php 
         $mapa_html = str_replace(
             ['width="100%"', 'height="250"'], 
             ['class="ubicamos_texto03"', ''], 
             $local['google_maps_iframe']
         );
         echo $mapa_html; 
      ?>
   </div>
   <br><br><br><br>
   
    <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
    <?php include 'footer.php'; ?>
</body>
</html>