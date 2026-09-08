<?php
require_once 'conexion.php';
session_start();

// 1. Configuración de Paginación (Máximo 9 productos)
$productos_por_pagina = 9;

// CAPTURA DE PÁGINA: Forzamos que sea un número entero siempre
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

// CÁLCULO DE OFFSET: Esto decide qué productos saltar
$offset = ($pagina_actual - 1) * $productos_por_pagina;

// 2. Filtros de búsqueda (Igual que antes)
$condiciones = ["stock > 0", "disponibilidad = 1"];
$parametros = [];

if (!empty($_GET['search'])) {
    $condiciones[] = "nombre_prod LIKE ?";
    $parametros[] = "%" . $_GET['search'] . "%";
}
if (!empty($_GET['category'])) {
    $condiciones[] = "id_categoria = ?";
    $parametros[] = (int)$_GET['category'];
}
if (!empty($_GET['price_range'])) {
    $rango_partes = explode('-', $_GET['price_range']);
    if (count($rango_partes) == 2) {
        $condiciones[] = "precio >= ? AND precio <= ?";
        $parametros[] = (float)$rango_partes[0];
        $parametros[] = (float)$rango_partes[1];
    }
}

$where_sql = implode(" AND ", $condiciones);

try {
    // 3. Contar total de productos para saber cuántas páginas hay
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE $where_sql");
    $stmt_count->execute($parametros);
    $total_productos_filtrados = (int)$stmt_count->fetchColumn();
    $total_paginas = ceil($total_productos_filtrados / $productos_por_pagina);

    // 4. Consulta de productos con LIMIT y OFFSET (Sintaxis compatible con MySQL/XAMPP)
    // Usamos los valores calculados directamente en el string para evitar problemas de tipos en PDO
    $select_sql = "SELECT * FROM productos WHERE $where_sql 
                   ORDER BY id_producto DESC 
                   LIMIT $productos_por_pagina OFFSET $offset";
    
    $stmt_select = $pdo->prepare($select_sql);
    $stmt_select->execute($parametros);
    $productos = $stmt_select->fetchAll();
    $filas_productos = array_chunk($productos, 3);

} catch (Exception $e) {
    die("Error en el catálogo: " . $e->getMessage());
}

// 5. Preparar parámetros para los botones (para no perder la búsqueda al cambiar de página)
$query_base = $_GET;
unset($query_base['p']); // Quitamos la p actual para poner la nueva en los botones
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>CATALOGO - EXO CUBE</title>
   <link href="estilos/catalogo.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   <style>
      /* Aseguramos que la paginación sea visible y clickeable */
      .paginacion-contenedor {
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 20px;
          margin: 50px 0;
          position: relative;
          z-index: 100;
      }
      
      }
      .info-pagina {
          font-family: Arial;
          font-size: 14px;
          color: #666;
      }
   </style>
</head>
<body>
   
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="catalogo_p">  
      <p>CATÁLOGO DE PRODUCTOS</p>
   </div>

   <!-- Formulario de Filtros -->
   <form action="catalogo.php" method="GET" id="form-filtros">
      <div class="caja-busqueda">
         <input type="text" name="search" placeholder="Buscar por nombre..." class="input-busqueda" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
         <button type="submit" class="boton-busqueda">🔍</button>
      </div>
      <br><br>
      
      <div class="contenedor-selector">
         <select name="category" class="selector-opciones" onchange="this.form.submit();">
            <option value="">Todas las Categorías</option>
            <?php foreach ($categorias_list as $cat): ?>
               <option value="<?php echo $cat['id_categoria']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['nombre_cat']); ?>
               </option>
            <?php endforeach; ?>
         </select>
      </div>
      <br>
      
      <div class="contenedor-selector2">
         <select name="price_range" class="selector-opciones" onchange="this.form.submit();">
            <option value="">Todos los Precios</option>
            <?php foreach ($rango_opciones as $rango): 
               $valor_rango = $rango['min'] . "-" . $rango['max'];
            ?>
               <option value="<?php echo $valor_rango; ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == $valor_rango) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($rango['label']); ?>
               </option>
            <?php endforeach; ?>
         </select>
      </div>
   </form>
   
   <br><br>

   <!-- Renderizado de Productos -->
   <?php 
   $global_index = 1;
   if (!empty($filas_productos)):
       foreach ($filas_productos as $index_fila => $productos_fila):
           // Clases dinámicas según tu CSS original
           if ($index_fila == 0) $clase_fila = "novedades_producto";
           elseif ($index_fila == 1) $clase_fila = "populares_producto";
           else $clase_fila = "promociones_producto";
   ?>
           <div class="<?php echo $clase_fila; ?>">  
              <?php foreach ($productos_fila as $item): 
                  $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/placeholder.png';
              ?>
                  <div class="p0<?php echo $global_index; ?>"><br>
                     <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="p00<?php echo $global_index; ?>">
                     <p class="p000<?php echo $global_index; ?>"><?php echo htmlspecialchars($item['nombre_prod']); ?></p>
                     <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto01"></a>
                  </div>
              <?php 
                  $global_index++;
              endforeach; ?>
           </div>
           <br><br>
   <?php 
       endforeach;
   else:
       echo "<center><p style='font-family:sans-serif; font-size:18px; color:#666;'>No hay productos que coincidan con los filtros.</p></center>";
   endif; 
   ?>

   <!-- Sistema de Paginación Inteligente Corregido para PHP 8 -->
<?php if ((int)$total_paginas > 1): ?>
    <div class="paginacion-contenedor">
       <?php 
       // Preparamos copias de los parámetros actuales
       $query_params_prev = $_GET;
       $query_params_next = $_GET;
       
       // Aseguramos que la página actual sea tratada como un número entero
       $pagina_num = (int)$pagina_actual; 
       ?>

       <!-- Botón ANTERIOR -->
       <?php if ($pagina_num > 1): 
           $query_params_prev['p'] = $pagina_num - 1; // Ahora es seguro restar
           $link_anterior = "catalogo.php?" . http_build_query($query_params_prev);
       ?>
           <a href="<?php echo $link_anterior; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
       <?php endif; ?>
       
       <!-- Botón SIGUIENTE -->
       <?php if ($pagina_num < (int)$total_paginas): 
           $query_params_next['p'] = $pagina_num + 1; // Ahora es seguro sumar
           $link_siguiente = "catalogo.php?" . http_build_query($query_params_next);
       ?>
           <a href="<?php echo $link_siguiente; ?>" class="btn-paginacion">SIGUIENTE</a>
       <?php endif; ?>
    </div>
<?php endif; ?>

   <br><br>
   <?php include 'footer.php'; ?>
  
</body>
</html>