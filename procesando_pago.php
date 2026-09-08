<?php
require_once 'conexion.php';
session_start();

// Capturar el ID del pedido para mantener el flujo
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>PROCESANDO PAGO...</title>
   <!-- Vinculamos la hoja de estilos de carga -->
   <link href="estilos/procesando_pago.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   
   <!-- JavaScript de automatización de secuencia temporal y auto-scroll -->
   <script>
      window.addEventListener('DOMContentLoaded', (event) => {
          // Paso 1: Iniciar validación de comprobante a los 2 segundos
          setTimeout(() => {
              // Rellenar visto de la primera tarea
              document.getElementById('check_1').innerText = '✓';
              document.getElementById('check_1').style.backgroundColor = '#69f0ae'; // Verde éxito
              
              // Desplazamiento automático de foco hacia la segunda tarea
              document.getElementById('row_2').scrollIntoView({ behavior: 'smooth', block: 'center' });
              
              // Paso 2: Validación del empaque/envoltura a los 4 segundos
              setTimeout(function() {
                  document.getElementById('check_2').innerText = "✓";
                  document.getElementById('check_2').style.backgroundColor = "#6fec6f";
                  
                  // Desplazamiento automático hacia la tercera tarea
                  document.getElementById('row_3').scrollIntoView({ behavior: 'smooth', block: 'center' });
                  
                  // Paso 3: Llegada a destino e impresión de boleta final
                  setTimeout(function() {
                      document.getElementById('check_3').innerText = "✓";
                      
                      // Cambiar texto de confirmación inferior
                      document.getElementById('pago_completado_banner').innerText = "Pago completado ✓";
                      document.getElementById('pago_completado_banner').style.backgroundColor = "#69f0ae";
                      
                      // Paso 4: Redirección automática final a pago_completado.php tras 1.5 segundos del último check
                      setTimeout(function() {
                          window.location = "pago_completado.php?id_pedido=<?php echo $id_pedido; ?>";
                      }, 1500);
                      
                  }, 2000); // Demora del paso 3: 2 segundos
                  
              }, 2000); // Demora del paso 2: 2 segundos
              
          }, 2000); // Demora del paso 1: 2 segundos
      });
   </script>
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>PROCESANDO PAGO...</p>
   </div>
   <br>

   <!-- TAREA 1: Validación de comprobante en el servidor -->
   <div class="loading-step-row" id="row_1">
      <img src="img/metodo-de-pago.png" alt="Paso 1" class="loading-step-img">
      <div class="checkbox-box" id="check_1"></div>
   </div>

   <!-- TAREA 2: Preparación física en almacén / empaquetado -->
   <div class="loading-step-row" id="row_2">
      <img src="img/carro-de-la-carretilla.png" alt="Paso 2" class="loading-step-img">
      <div class="checkbox-box" id="check_2"></div>
   </div>

   <!-- TAREA 3: Sincronización final / Generador de Boletas -->
   <div class="loading-step-row" id="row_3">
      <img src="img/procesando.png" alt="Paso 3" class="loading-step-img">
      <div class="checkbox-box" id="check_3"></div>
   </div>

   <!-- Recuadro final de confirmación de "Pago completado" -->
   <div class="final-confirm-box" id="row_final">
      <p id="pago_completado_banner" style="margin: 0; padding: 10px; transition: background-color 0.3s;">Pago en proceso...</p>
   </div>

   <br>
 
       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>