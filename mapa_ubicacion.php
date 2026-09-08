<?php
require_once 'conexion.php';
session_start();

// 1. Configurar Paginación (Máximo 9 locales por página)
$locales_por_pagina = 9;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $locales_por_pagina;

try {
    // Consultar el texto de reseña/experiencia corporativo
    $stmt_info = $pdo->query("SELECT experiencia FROM informacion_tienda WHERE id_info = 1");
    $info_tienda = $stmt_info->fetch();

    // CORRECCIÓN 1: El Gran Mapa Superior cargará dinámicamente el primer local que esté ENCENDIDO (activo = 1)
    $stmt_main_map = $pdo->query("SELECT google_maps_iframe FROM locales_tienda WHERE activo = 1 ORDER BY id_local ASC LIMIT 1");
    $mapa_principal = $stmt_main_map->fetchColumn();

    // CORRECCIÓN 2: Contar únicamente los locales que estén ENCENDIDOS para la paginación
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM locales_tienda WHERE activo = 1");
    $total_locales = $stmt_count->fetchColumn();

    // CORRECCIÓN 3: Consultar los locales paginados filtrando únicamente los ENCENDIDOS
    $stmt_select = $pdo->prepare("SELECT * FROM locales_tienda WHERE activo = 1 ORDER BY id_local ASC LIMIT ? OFFSET ?");
    $stmt_select->bindValue(1, $locales_por_pagina, PDO::PARAM_INT);
    $stmt_select->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt_select->execute();
    $locales = $stmt_select->fetchAll();

    // Calcular páginas totales
    $total_paginas = ceil($total_locales / $locales_por_pagina);

} catch (Exception $e) {
    die("Error al procesar la información de ubicación y mapas.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>MAPA DE UBICACION</title>
   <!-- Vinculamos la nueva hoja de estilos optimizada -->
   <link href="estilos/mapa_ubicacion.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>MAPA DE UBICACIÓN</p>
   </div>
   <br><br><br><br><br><br>
   
   <!-- Gran Mapa Principal Superior Dinámico desde locales_tienda -->
   <?php 
      if (!empty($mapa_principal)) {
         // Ajustamos la clase de tu hoja de estilos sobre el iframe dinámico
         $mapa_principal_html = str_replace(
             ['width="100%"', 'height="250"'], 
             ['class="mapa_ubicacion"', ''], 
             $mapa_principal
         );
         echo $mapa_principal_html; 
      } else {
         echo "<center><p style='color:#666;'>Mapa principal no disponible.</p></center>";
      }
   ?>

   <br><br><br>
   
   <!-- Cuadro de reseña/experiencia de la tienda dinámica (informacion_tienda) -->
   <div class="experiencia_texto">  
      <p><?php echo htmlspecialchars($info_tienda['experiencia']); ?></p>
   </div>
   <br><br><br>

   <!-- Bucle dinámico para listar todas tus sucursales con su respectiva dirección y mapa -->
   <?php if (!empty($locales)): ?>
       <?php foreach ($locales as $local): ?>
           <div class="ubicamos">  
              <p>¿DÓNDE SE UBICA LA <?php echo htmlspecialchars($local['nombre_local']); ?>?</p>
           </div>
           <br><br>
           <div class="ubicamos_texto">  
              <div class="ubicamos_texto02">
                 <!-- Dirección dinámica del local -->
                 <p><?php echo htmlspecialchars($local['direccion']); ?></p>
              </div>
              <!-- Mapa interactivo dinámico de este local -->
              <?php 
                 $mapa_local_html = str_replace(
                     ['width="100%"', 'height="250"'], 
                     ['class="ubicamos_texto03"', ''], 
                     $local['google_maps_iframe']
                 );
                 echo $mapa_local_html; 
              ?>
           </div>
           <br><br><br>
       <?php endforeach; ?>
   <?php else: ?>
       <center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay locales físicos registrados actualmente.</p></center><br><br>
   <?php endif; ?>

   <!-- Sistema Dinámico de Paginación para las ubicaciones -->
   <?php if ($total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ($pagina_actual > 1): ?>
              <a href="mapa_ubicacion.php?p=<?php echo $pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ($pagina_actual < $total_paginas): ?>
              <a href="mapa_ubicacion.php?p=<?php echo $pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>
<br>
    <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
    <?php include 'footer.php'; ?>
</body>
</html>