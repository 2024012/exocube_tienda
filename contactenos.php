<?php
require_once 'conexion.php';
session_start();

// 1. Configurar Paginación (Máximo 9 enlaces cuadro por página)
$enlaces_por_pagina = 9;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $enlaces_por_pagina;

try {
     // CORRECCIÓN: Consultar nombres y horarios únicamente de las sucursales ENCENDIDAS (activo = 1)
     $stmt_hours = $pdo->query("SELECT nombre_local, horario_atencion FROM locales_tienda WHERE activo = 1 ORDER BY id_local ASC");
     $locales_horas = $stmt_hours->fetchAll();

    // Contar el total de enlaces de contacto
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM enlaces_contacto");
    $total_enlaces = $stmt_count->fetchColumn();

    // CORRECCIÓN: Filtrar los enlaces por paginación exigiendo que estén activos = 1
    $stmt_select = $pdo->prepare("SELECT * FROM enlaces_contacto WHERE activo = 1 ORDER BY id_contacto ASC LIMIT ? OFFSET ?");
    $stmt_select->bindValue(1, $enlaces_por_pagina, PDO::PARAM_INT);
    $stmt_select->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt_select->execute();
    $contactos = $stmt_select->fetchAll();

    // Calcular páginas totales
    $total_paginas = ceil($total_enlaces / $enlaces_por_pagina);

    // Agrupar los enlaces de 3 en 3 para maquetación
    $filas_contactos = array_chunk($contactos, 3);

} catch (Exception $e) {
    // Si necesitas depurar el error detallado de la base de datos localmente, puedes descomentar la siguiente línea:
    // die("Error detallado: " . $e->getMessage());
    die("Error al procesar la información de contacto.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>CONTÁCTENOS</title>
   <!-- Vinculamos la hoja de estilos optimizada -->
   <link href="estilos/contactenos.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p>CONTÁCTENOS</p>
   </div><br>

   <?php 
   $global_index = 1;

   // 2. Renderizar dinámicamente las filas de enlaces de contacto
   if (!empty($filas_contactos)):
       foreach ($filas_contactos as $index_fila => $contactos_fila):
           if ($index_fila == 0) $clase_fila = "novedades_producto";
           elseif ($index_fila == 1) $clase_fila = "populares_producto";
           else $clase_fila = "promociones_producto";
   ?>
           <div class="<?php echo $clase_fila; ?>">  
              <?php foreach ($contactos_fila as $item): 
                  $clase_div = "p0" . $global_index;
                  $clase_img = "p00" . $global_index;
                  $clase_p = "p000" . $global_index;
              ?>
                  <div class="<?php echo $clase_div; ?>"><br>
                     <img src="<?php echo htmlspecialchars($item['icono_url']); ?>" alt="Icono de contacto" class="<?php echo $clase_img; ?>">
                     <p class="<?php echo $clase_p; ?>"><?php echo htmlspecialchars($item['etiqueta']); ?></p>
                     <a href="<?php echo htmlspecialchars($item['enlace']); ?>" class="enlace-producto01" target="_blank"></a>
                  </div>
              <?php 
                  $global_index++;
              endforeach; ?>
           </div>
           <br><br><br>
   <?php 
       endforeach;
   else:
       echo "<center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay enlaces de contacto disponibles en este momento.</p></center><br><br>";
   endif; 
   ?>

   <!-- 3. Sistema Dinámico de Paginación para los Enlaces Cuadro -->
   <?php if ($total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ($pagina_actual > 1): ?>
              <a href="contactenos.php?p=<?php echo $pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ($pagina_actual < $total_paginas): ?>
              <a href="contactenos.php?p=<?php echo $pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>

   <div class="atendemos">  
      <p>¿CUÁNDO ATENDEMOS?</p>
   </div>
   
   
   <!-- CORRECCIÓN: Bucle para mostrar el horario de atención de cada sucursal de forma independiente -->
   <?php if (!empty($locales_horas)): ?>
       <?php foreach ($locales_horas as $local): ?>
           <div class="atendemos_texto" style="margin-top: 15px; margin-bottom: 25px; width: 60%;">  
              <!-- Nombre de la Sucursal -->
              <p class="ate02" style="font-weight: bold; color: #333; text-transform: uppercase;"><?php echo htmlspecialchars($local['nombre_local']); ?></p>
              <!-- Horario Específico de esta sucursal -->
              <p class="ate03" style="font-size: 28px;"><?php echo htmlspecialchars($local['horario_atencion']); ?></p>
           </div><br>
       <?php endforeach; ?>
   <?php else: ?>
       <center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay horarios de atención disponibles.</p></center><br><br>
   <?php endif; ?>
   <br><br>
  <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
  <?php include 'footer.php'; ?>

</body>
</html>