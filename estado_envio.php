<?php
require_once 'conexion.php';
session_start();

$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    // Consultar la boleta de compra para traer su fecha_pedido
    $stmt_ped = $pdo->prepare("SELECT fecha_pedido FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("Boleta no encontrada.");
    }

    // Calcular el tiempo transcurrido desde que se hizo el pedido en horas decimales
    $fecha_pedido = new DateTime($pedido['fecha_pedido']);
    $fecha_actual = new DateTime(); // Hora del servidor actual
    $intervalo = $fecha_pedido->diff($fecha_actual);
    
    // Convertir días, horas y minutos transcurridos a formato de Horas Totales
    $horas_transcurridas = ($intervalo->days * 24) + $intervalo->h + ($intervalo->i / 60);

} catch (Exception $e) {
    die("Error al consultar el estatus de entrega.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>ESTADO DE ENVÍO</title>
   <!-- Vinculamos la hoja de estilos de envío -->
   <link href="estilos/estado_envio.css" rel="stylesheet">
   
   <script>
      // Inyectar el número decimal de horas transcurridas desde PHP de forma segura
      const horasTranscurridas = <?php echo json_encode($horas_transcurridas); ?>;

      window.addEventListener('DOMContentLoaded', (event) => {
          
          // PASO 1: Escaneo de la primera imagen (Cargando productos - Umbral: 2 horas)
          setTimeout(() => {
              let check1 = document.getElementById('check_1');
              if (horasTranscurridas >= 2) {
                  check1.innerText = '✓';
                  check1.style.backgroundColor = '#69f0ae'; // Verde éxito
              } else {
                  check1.innerText = '✗';
                  check1.style.backgroundColor = '#ff8a80'; // Rojo aspa
              }
              
              // Desplazamiento de foco suave hacia el paso 2
              document.getElementById('row_2').scrollIntoView({ behavior: 'smooth', block: 'center' });
              
              // PASO 2: Escaneo de la segunda imagen (Enviando por transporte - Umbral: 4 horas)
              setTimeout(() => {
                  let check2 = document.getElementById('check_2');
                  if (horasTranscurridas >= 4) {
                      check2.innerText = '✓';
                      check2.style.backgroundColor = '#69f0ae';
                  } else {
                      check2.innerText = '✗';
                      check2.style.backgroundColor = '#ff8a80';
                  }
                  
                  // Desplazamiento hacia el paso 3
                  document.getElementById('row_3').scrollIntoView({ behavior: 'smooth', block: 'center' });
                  
                  // PASO 3: Escaneo de la tercera imagen (Llegada a destino - Umbral: 6 horas)
                  setTimeout(() => {
                      let check3 = document.getElementById('check_3');
                      if (horasTranscurridas >= 6) {
                          check3.innerText = '✓';
                          check3.style.backgroundColor = '#69f0ae';
                      } else {
                          check3.innerText = '✗';
                          check3.style.backgroundColor = '#ff8a80';
                      }
                      
                      // Desplazamiento final hacia los botones de acción inferior
                      document.getElementById('row_botones').scrollIntoView({ behavior: 'smooth', block: 'center' });
                      
                      // Habilitar o inhabilitar botones de forma inteligente según la base de datos
                      if (horasTranscurridas >= 6) {
                          // Caso éxito total (6+ horas): Habilitar "Envío Completado", deshabilitar "En proceso"
                          document.getElementById('btn_completado_link').classList.remove('disabled');
                          document.getElementById('btn_proceso_link').classList.add('disabled');
                      } else {
                          // Caso pendiente (Menos de 6 horas): Habilitar "Envío en Proceso", deshabilitar "Completado"
                          document.getElementById('btn_proceso_link').classList.remove('disabled');
                          document.getElementById('btn_completado_link').classList.add('disabled');
                      }
                      
                  }, 1500); // 1.5s
              }, 1500); // 1.5s
          }, 1500); // 1.5s
      });
   </script>
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>ESTADO DE ENVIO</p>
   </div>
   <br><br><br><br>

   <!-- PASO 1: Cargando productos (img/camion.png) -->
   <div class="loading-step-row" id="row_1" style="border: 4px solid rgb(167, 73, 16); padding: 15px; width: 70%; margin: 0 auto;">
      <div style="flex:1; text-align:left; font-family:'Alfaqix'; font-size:24px; padding-left: 20px; font-weight:bold;">Cargando productos...</div>
      <img src="img/camion.png" alt="Camión" class="loading-step-img" style="width: 100px; height: 100px; margin-right: 40px;">
      <div class="checkbox-box" id="check_1" style="margin-right:20px;"></div>
   </div>
   <br><br>

   <!-- PASO 2: Enviando por transporte (img/camion.png) -->
   <div class="loading-step-row" id="row_2" style="border: 4px solid rgb(167, 73, 16); padding: 15px; width: 70%; margin: 0 auto;">
      <div style="flex:1; text-align:left; font-family:'Alfaqix'; font-size:24px; padding-left: 20px; font-weight:bold;">Enviando por transporte...</div>
      <img src="img/camion-de-reparto.png" alt="Transporte" class="loading-step-img" style="width: 100px; height: 100px; margin-right: 40px;">
      <div class="checkbox-box" id="check_2" style="margin-right:20px;"></div>
   </div>
   <br><br>

   <!-- PASO 3: Llegada a destino (img/envase.png) -->
   <div class="loading-step-row" id="row_3" style="border: 4px solid rgb(167, 73, 16); padding: 15px; width: 70%; margin: 0 auto;">
      <div style="flex:1; text-align:left; font-family:'Alfaqix'; font-size:24px; padding-left: 20px; font-weight:bold;">Llegada a destino...</div>
      <img src="img/envase.png" alt="Envase/Destino" class="loading-step-img" style="width: 100px; height: 100px; margin-right: 40px;">
      <div class="checkbox-box" id="check_3" style="margin-right:20px;"></div>
   </div>
   <br><br><br><br>

   <!-- Botonera de acciones inteligentes (Se deshabilitarán mediante JS con la clase .disabled) -->
   <div class="botones-envio-seccion" id="row_botones">
      
      <!-- BOTÓN 1: ENVÍO EN PROCESO (Clase desactivada por defecto, se activa en JS si horas < 6) -->
      <a href="envio_proceso.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-envio-card btn-proceso disabled" id="btn_proceso_link" target="_blank">
         <h3>ENVIO EN PROCESO</h3>
         <p>No se aceptan devoluciones</p>
      </a>
      
      <!-- BOTÓN 2: ENVÍO COMPLETADO (Clase desactivada por defecto, se activa en JS si horas >= 6) -->
      <a href="envio_completado.php?id_pedido=<?php echo $id_pedido; ?>" class="btn-envio-card btn-completado disabled" id="btn_completado_link" target="_blank">
         <h3>ENVIO COMPLETADO</h3>
         <p>No se aceptan devoluciones</p>
      </a>
      
   </div>
   <br><br><br><br>
   
         <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
         <?php include 'footer.php'; ?>
</body>
</html>