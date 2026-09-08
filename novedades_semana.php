<?php
require_once 'conexion.php';
session_start();

// 1. Configurar Paginación (9 productos por página)
$productos_por_pagina = 9;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = (int)(($pagina_actual - 1) * $productos_por_pagina);

try {
    // 2. Consulta A: Contar productos ingresados en los últimos 14 días
    $stmt_count = $pdo->query("
        SELECT COUNT(*) FROM productos 
        WHERE fecha_ingreso >= NOW() - INTERVAL 14 DAY AND disponibilidad = 1
    ");
    $total_recientes = $stmt_count->fetchColumn();

    if ($total_recientes > 0) {
        // MODO ESTÁNDAR: Mostrar los del plazo de 14 días
        $total_productos = $total_recientes;
        
        // CORRECCIÓN DEFINITIVA: Inyectamos los enteros validados de LIMIT y OFFSET directamente en la consulta
        $select_sql = "
            SELECT * FROM productos 
            WHERE fecha_ingreso >= NOW() - INTERVAL 14 DAY AND disponibilidad = 1 
            ORDER BY fecha_ingreso DESC 
            LIMIT " . (int)$productos_por_pagina . " OFFSET " . (int)$offset;
    } else {
        // MODO FALLBACK: No hay nada nuevo en 2 semanas, mostrar los últimos 18 productos agregados
        $total_productos = 18; // Límite estricto de 18
        
        // CORRECCIÓN DEFINITIVA: Inyectamos los enteros validados de LIMIT y OFFSET directamente en la consulta
        $select_sql = "
            SELECT * FROM productos 
            WHERE disponibilidad = 1 
            ORDER BY id_producto DESC 
            LIMIT " . (int)$productos_por_pagina . " OFFSET " . (int)$offset;
    }

    // Al no tener marcadores de entrada del usuario en el WHERE, podemos consultar directamente de forma segura
    $productos = $pdo->query($select_sql)->fetchAll();

    // Calcular total de páginas
    $total_paginas = ceil($total_productos / $productos_por_pagina);

    // Agrupar los 9 productos en sub-arreglos de 3 para maquetar tus filas (novedades, populares, promociones)
    $filas_productos = array_chunk($productos, 3);

} catch (Exception $e) {
    die("Error al procesar la lista de novedades: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>NOVEDADES DE LA SEMANA</title>
   <!-- Vinculamos la hoja de estilos modificada -->
   <link href="estilos/novedades_semana.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">

   
   <style>
      /* =========================================================================
         CORRECCIÓN UX CRÍTICA: Elevar los botones de paginación para que los
         enlaces transparentes e invisibles de las fotos de arriba no los tapen.
         ========================================================================= */
      .paginacion-contenedor {
          position: relative !important;
          z-index: 9999 !important; /* Capa súper elevada */
          display: flex !important;
          justify-content: center !important;
          margin: 40px auto !important;
      }
      .btn-paginacion {
          position: relative !important;
          z-index: 10000 !important; /* Capa por encima de todo */
          cursor: pointer !important;
          pointer-events: auto !important; /* Asegura la acción del clic */
      }
   </style>
</head>
<body>
  
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="bienvenida">  
      <p>Trato directo, cercano y gran variedad de productos al por menor</p>
   </div>
   <br>
   <div class="somos">  
      <p>NOVEDADES DE LA SEMANA</p>
   </div>
   <br>

   <?php 
   // Variables globales para mapear las clases estáticas originales (p01, p001, etc.)
   $global_index = 1;

   // 3. Renderizar las filas dinámicamente según la maquetación de tu HTML original
   if (!empty($filas_productos)):
       foreach ($filas_productos as $index_fila => $productos_fila):
           // Determinar la clase del contenedor de fila correspondiente
           if ($index_fila == 0) $clase_fila = "novedades_producto";
           elseif ($index_fila == 1) $clase_fila = "populares_producto";
           else $clase_fila = "promociones_producto";
   ?>
           <div class="<?php echo $clase_fila; ?>">  
              <?php foreach ($productos_fila as $item): 
                  // Mapear tus clases y IDs dinámicamente de forma ordenada (p01, p001, p0001, etc.)
                  $clase_div = "p0" . $global_index;
                  $clase_img = "p00" . $global_index;
                  $clase_p = "p000" . $global_index;
                  $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/57.png';
              ?>
                  <div class="<?php echo $clase_div; ?>"><br>
                     <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="<?php echo $clase_img; ?>">
                     <p class="<?php echo $clase_p; ?>"><?php echo htmlspecialchars($item['nombre_prod']); ?></p>
                     <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto01" target="_blank"></a>
                  </div>
              <?php 
                  $global_index++;
              endforeach; ?>
           </div>
           <br><br><br>
   <?php 
       endforeach;
   else:
       echo "<center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay novedades disponibles en este momento.</p></center><br>";
   endif; 
   ?>

   <!-- 4. Sistema Dinámico de Paginación Corregido -->
   <?php if ((int)$total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ((int)$pagina_actual > 1): ?>
              <!-- CORRECCIÓN: Forzamos tipo de dato entero en la resta -->
              <a href="novedades_semana.php?p=<?php echo (int)$pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ((int)$pagina_actual < (int)$total_paginas): ?>
              <!-- CORRECCIÓN: Forzamos tipo de dato entero en la suma -->
              <a href="novedades_semana.php?p=<?php echo (int)$pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>
   <br><br><br>

   <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
   <?php include 'footer.php'; ?>
</body>
</html>