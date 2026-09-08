<?php
require_once 'conexion.php';
session_start();

// Capturar el ID del pedido para pasarlo como parámetro a los siguientes enlaces
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>PAGO COMPLETADO</title>
   <!-- Vinculamos la hoja de estilos de éxito -->
   <link href="estilos/pago_completado.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>PAGO COMPLETADO</p>
   </div>
  <br><br><br>
   
   <!-- Icono Vectorial SVG de la Billetera con Visto Verde (Idéntico a tu captura) -->
   <div class="wallet-container">
   <img src="img/compras.png" alt="tienda" class="wallet">
      
   </div>
   <br><br>
   
   <!-- Botonera Doble en paralelo alineada de forma simétrica -->
   <div class="botones-completado-seccion">
      
      <!-- BOTÓN 1: VER BOLETA DE PAGO (Lleva al impresor de boletas pasándole el ID) -->
      <a href="imprimir_boleta.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-completado-card" target="_blank">
         <h3>VER BOLETA DE PAGO</h3>
         <p>Verifique su correo</p>
      </a>
      
      <!-- BOTÓN 2: REVISAR ESTADO DE ENVÍO (Lears al estatus del courier) -->
      <a href="estado_envio.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-completado-card" target="_blank">
         <h3>REVISAR ESTADO DE ENVIO</h3>
         <p>Seguimiento de entrega</p>
      </a>
      
   </div>
   <br><br><br>

       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>