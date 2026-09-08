<?php
require_once 'conexion.php';
session_start();

try {
    // Consultar la información del negocio en la Base de Datos (Mapea el ID 1)
    $stmt_info = $pdo->query("SELECT * FROM informacion_tienda WHERE id_info = 1");
    $info = $stmt_info->fetch();

    if (!$info) {
        die("Error: No se encontró la información general de la tienda.");
    }
    // 2. Consultar todos los locales registrados para la empresa (relación 1:N)
     // CORRECCIÓN: Consultar únicamente los locales que estén ENCENDIDOS (activo = 1)
     $stmt_locales = $pdo->prepare("SELECT * FROM locales_tienda WHERE id_info = ? AND activo = 1 ORDER BY id_local ASC");
     $stmt_locales->execute([$info['id_info']]);
     $locales = $stmt_locales->fetchAll();

} catch (Exception $e) {
    die("Error al conectar con la base de datos.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>NOSOTROS</title>
   <!-- Vinculamos la nueva hoja de estilos optimizada con soporte de iframe -->
   <link href="estilos/nosotros.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
  
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>¿QUIÉNES SOMOS?</p>
   </div>
 <br><br>
   <div class="somos_texto">  
      <!-- Quienes Somos dinámico de la base de datos -->
      <p><?php echo htmlspecialchars($info['quienes_somos']); ?></p>
   </div>
   <br><br><br><br>
   <div class="experiencia">  
      <p>¿QUÉ EXPERIENCIA OFRECEMOS?</p>
   </div><br><br>
   <div class="experiencia_texto">  
      <!-- Detalle de experiencia dinámico de la base de datos -->
      <p><?php echo htmlspecialchars($info['experiencia']); ?></p>
   </div>
   <br><br><br><br>
 <!-- BUCLE FOREACH: Itera dinámicamente según la cantidad de locales en la base de datos -->
 <?php if (!empty($locales)): ?>
       <?php foreach ($locales as $index => $local): ?>
           
           <!-- Título dinámico para cada sucursal -->
           <div class="ubicamos">  
              <p>¿DÓNDE SE UBICA LA <?php echo htmlspecialchars($local['nombre_local']); ?>?</p>
           </div>
          <br><br>
           <div class="ubicamos_texto">  
              <div class="ubicamos_texto02">
                 <!-- Dirección dinámica del local actual -->
                 <p><?php echo htmlspecialchars($local['direccion']); ?></p>
              </div>
              <!-- Mapa interactivo dinámico de este local -->
              <?php 
                 $mapa_html = str_replace(
                     ['width="100%"', 'height="250"'], 
                     ['class="ubicamos_texto03"', ''], 
                     $local['google_maps_iframe']
                 );
                 echo $mapa_html; 
              ?>
           </div>
       <br><br><br>
           <div class="atendemos">  
              <p>¿CUÁNDO ATIENDE ESTA SUCURSAL?</p>
           </div>
           <br><br>
           <div class="atendemos_texto">  
              <p class="ate02">HORARIO DE ATENCION</p>
              <!-- Horario de atención específico de este local -->
              <p class="ate03"><?php echo htmlspecialchars($local['horario_atencion']); ?></p>
           </div>
          <br><br><br><br>

       <?php endforeach; ?>
   <?php else: ?>
       <center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay locales físicos registrados actualmente.</p></center><br><br>
   <?php endif; ?>
   <br><br><br>

    <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
    <?php include 'footer.php'; ?>
</body>