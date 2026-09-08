<?php
require_once 'conexion.php';
session_start();

// 1. Configurar Paginación (9 productos por página)
$productos_por_pagina = 9;

// Capturamos la variable de la URL de forma segura
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

// Calculamos el desplazamiento
$offset = (int)(($pagina_actual - 1) * $productos_por_pagina);

try {
    // Contar los productos que tienen descuento activo y stock
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM productos WHERE descuento > 0 AND stock > 0 AND disponibilidad = 1");
    $total_promociones = $stmt_count->fetchColumn();

    // Calcular total de páginas
    $total_paginas = ceil($total_promociones / $productos_por_pagina);

    // CORRECCIÓN FUNCIONAL: Sintaxis LIMIT offset, limite (Evita bugs en XAMPP)
    $select_sql = "
        SELECT * FROM productos 
        WHERE descuento > 0 AND stock > 0 AND disponibilidad = 1 
        ORDER BY descuento DESC, id_producto DESC 
        LIMIT {$offset}, {$productos_por_pagina}";

    // Ejecutamos la consulta
    $productos = $pdo->query($select_sql)->fetchAll();

    // Agrupar de 3 en 3 para maquetación
    $filas_productos = array_chunk($productos, 3);

} catch (Exception $e) {
    die("Error al procesar la lista de promociones: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>PROMOCIONES ACTUALES</title>
   <!-- Tus hojas de estilo originales intocables -->
   <link href="estilos/promociones_actuales.css" rel="stylesheet">
   
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   
   <style>
      /* Solo nos aseguramos de que tu contenedor original esté por encima */
      .paginacion-contenedor {
          position: relative !important;
          z-index: 9999 !important; 
          display: flex !important;
          justify-content: center !important;
          margin: 40px auto !important;
      }
      .btn-paginacion {
          position: relative !important;
          z-index: 10000 !important;
          cursor: pointer !important;
          pointer-events: auto !important;
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
      <p>PROMOCIONES ACTUALES</p>
   </div>
   <br>

   <?php 
   $global_index = 1;

   if (!empty($filas_productos)):
       foreach ($filas_productos as $index_fila => $productos_fila):
           if ($index_fila == 0) $clase_fila = "novedades_producto";
           elseif ($index_fila == 1) $clase_fila = "populares_producto";
           else $clase_fila = "promociones_producto";
   ?>
           <div class="<?php echo $clase_fila; ?>">  
              <?php foreach ($productos_fila as $item): 
                  $clase_div = "p0" . $global_index;
                  $clase_img = "p00" . $global_index;
                  $clase_p = "p000" . $global_index;
                  $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/57.png';
              ?>
                  <!-- CORRECCIÓN DE CLICK: Mantiene el enlace transparente DENTRO de su caja -->
                  <div class="<?php echo $clase_div; ?>" style="position: relative;"><br>
                     <!-- Sello de descuento en la esquina superior derecha -->
                     <div class="sello-descuento">-<?php echo $item['descuento']; ?>%</div>
                     
                     <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="<?php echo $clase_img; ?>">
                     
                     <!-- Nombre -->
                     <p class="<?php echo $clase_p; ?>" style="line-height:1.2;">
                        <?php echo htmlspecialchars($item['nombre_prod']); ?><br>
                     </p>
                     
                     <!-- El enlace transparente limitando su tamaño absoluto -->
                     <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto01" target="_blank" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></a>
                  </div>
              <?php 
                  $global_index++;
              endforeach; ?>
           </div>
           <br><br><br>
   <?php 
       endforeach;
   else:
       echo "<center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay promociones disponibles en este momento.</p></center><br><br>";
   endif; 
   ?>

   <!-- Sistema de Paginación Funcional (Mantiene tu diseño CSS intacto) -->
   <?php if ((int)$total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ((int)$pagina_actual > 1): ?>
              <a href="?p=<?php echo (int)$pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ((int)$pagina_actual < (int)$total_paginas): ?>
              <a href="?p=<?php echo (int)$pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>
   <br><br><br>
 
   <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
   <?php include 'footer.php'; ?>
</body>
</html>