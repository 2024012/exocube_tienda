<?php
require_once 'conexion.php';
session_start();

// Capturar el ID de la boleta de compra (por defecto 2 para pruebas)
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    // Consultar los datos del pedido en la base de datos (con su celular y dirección)
    $stmt_ped = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("Boleta no encontrada.");
    }

} catch (Exception $e) {
    die("Error al consultar la boleta de entrega.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>ENVÍO COMPLETADO</title>
   <!-- Vinculamos la hoja de estilos de éxito de envío -->
   <link href="estilos/envio_completado.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>ENVIO COMPLETADO</p>
   </div>
   <br><br><br>
   
   <!-- Imagen Dinámica del Repartidor (chico-de-entrega.png) -->
   <img src="img/chico-de-entrega.png" alt="Repartidor de exo_cube" class="repartidor-img">
   <br>

  
   <!-- ... (Sección del cuadro de reparto de envio_completado.php) ... -->
   
   <div class="contacto-reparto-box">
      <p style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px;">INFORMACION DE REPARTO</p>
      <!-- CORRECCIÓN: Nombre dinámico real del cliente -->
      <p>• CLIENTE DESTINATARIO: <span style="font-family: Alfaqix; font-size: 20px; font-weight: bold;"><?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente General'); ?></span></p>
      <p>• ZONA DE ENVÍO: <span style="font-family: Alfaqix; font-size: 20px;"><?php echo htmlspecialchars($pedido['tipo_entrega']); ?></span></p>
      <p>• DIRECCIÓN EXACTA: <span style="font-family: Alfaqix; font-size: 20px; color: #444; font-weight: bold;">
          <?php echo !empty($pedido['direccion_entrega']) ? htmlspecialchars($pedido['direccion_entrega']) : 'Recojo directo en tienda física (Sin delivery)'; ?>
      </span></p>
      <p>• CELULAR DE CONTACTO: <span style="font-family: Alfaqix; font-size: 20px; color: green; font-weight: bold;"><?php echo htmlspecialchars($pedido['cliente_celular'] ?? 'No registrado'); ?></span></p>
      <p>• METODO DE PAGO: <span style="font-family: Alfaqix; font-size: 20px;"><?php echo htmlspecialchars($pedido['metodo_pago']); ?></span></p>
   </div> 
   <br><br><br>
   
   <!-- Botonera Doble Funcional alineada lado a lado -->
   <div class="botones-envio-seccion">
      
      <!-- BOTÓN 1: CHEQUEE LUGAR DE ENTREGA (Lleva al visor de mapas de entrega sin el mapa grande superior) -->
      <a href="lugar_entrega.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-envio-card" target="_blank">
         <h3>CHEQUEE LUGAR DE ENTREGA</h3>
         <p>Verifique su correo</p>
      </a>
      
      <!-- BOTÓN 2: ESTADO DE ENVÍO (Lleva de vuelta al estado_envio.php con el paso a paso) -->
      <a href="estado_envio.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-envio-card" target="_blank">
         <h3>ESTADO DE ENVIO</h3>
         <p>Revisar tracker de entrega</p>
      </a>
      
   </div>
   <br><br><br><br>
   
      <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
      <?php include 'footer.php'; ?>
</body>
</html>