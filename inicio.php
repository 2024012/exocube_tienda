<?php
require_once 'conexion.php';
session_start();

try {
    // -------------------------------------------------------------------------
    // CONSULTA 1: NOVEDADES (Últimos 14 días o fallback de últimos 9 del inventario)
    // -------------------------------------------------------------------------
    $stmt_count_rec = $pdo->query("
        SELECT COUNT(*) FROM productos 
        WHERE fecha_ingreso >= NOW() - INTERVAL 14 DAY AND disponibilidad = 1
    ");
    $total_recientes = $stmt_count_rec->fetchColumn();

    if ($total_recientes > 0) {
        $stmt_novedades = $pdo->query("
            SELECT * FROM productos 
            WHERE fecha_ingreso >= NOW() - INTERVAL 14 DAY AND disponibilidad = 1 
            ORDER BY fecha_ingreso DESC LIMIT 9
        ");
    } else {
        $stmt_novedades = $pdo->query("
            SELECT * FROM productos 
            WHERE disponibilidad = 1 
            ORDER BY id_producto DESC LIMIT 9
        ");
    }
    $novedades = $stmt_novedades->fetchAll();
    $bloques_novedades = array_chunk($novedades, 3); // Agrupar de 3 en 3

    // -------------------------------------------------------------------------
    // CONSULTA 2: PRODUCTOS POPULARES (Más vendidos con stock disponible, máximo 9)
    // -------------------------------------------------------------------------
    $stmt_populares = $pdo->query("
        SELECT p.*, COALESCE(SUM(d.cantidad), 0) as total_vendido 
        FROM productos p 
        LEFT JOIN detalle_pedidos d ON p.id_producto = d.id_producto 
        WHERE p.stock > 0 AND p.disponibilidad = 1 
        GROUP BY p.id_producto 
        ORDER BY total_vendido DESC, p.nombre_prod ASC LIMIT 9
    ");
    $populares = $stmt_populares->fetchAll();
    $bloques_populares = array_chunk($populares, 3);

    // -------------------------------------------------------------------------
    // CONSULTA 3: PROMOCIONES ACTUALES (Descuento > 0 con stock disponible, máximo 9)
    // -------------------------------------------------------------------------
    $stmt_promos = $pdo->query("
        SELECT * FROM productos 
        WHERE descuento > 0 AND stock > 0 AND disponibilidad = 1 
        ORDER BY descuento DESC, id_producto DESC LIMIT 9
    ");
    $promos = $stmt_promos->fetchAll();
    $bloques_promos = array_chunk($promos, 3);

} catch (Exception $e) {
    die("Error al cargar la información de la página de inicio.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>INICIO</title>
   <!-- Vinculamos la nueva hoja de estilos optimizada -->
   <link href="estilos/inicio.css" rel="stylesheet">
   <!-- 1. Le dice al celular: "Ajusta el ancho de la página al ancho de tu pantalla" -->
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   <!-- Ventana Emergente de Bienvenida (Modal) -->
   <div id="modal-bienvenida" class="modal-overlay">
      <div class="modal-content">
        <button class="modal-cerrar" onclick="cerrarModal()">&times;</button>
        <h2>¡Te damos la bienvenida!</h2>
        <p>Gracias por visitar nuestra pagina. Descubre todas las novedades que tenemos para ti hoy.</p>
        <button class="modal-boton-accion" onclick="cerrarModal()">Empezar</button>
      </div>
   </div>
 
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="bienvenida">  
      <p>Trato directo, cercano y gran variedad de productos al por menor</p>
   </div>
   <br><br><br>
   
   <div class="novedades">  
      <p>NOVEDADES DE LA SEMANA</p>
      <a href="novedades_semana.php" class="enlace-novedades" target="_blank"></a>
   </div>
   <br><br><br>
   
   <!-- CARRUSEL 1: NOVEDADES DE LA SEMANA -->
   <div class="carrusel-contenedor">
      <button class="carrusel-btn prev" onclick="cambiarBloque(this, -1)">&#10094;</button>
      <button class="carrusel-btn next" onclick="cambiarBloque(this, 1)">&#10095;</button>
      
      <div class="carrusel-track">
         <?php if (!empty($bloques_novedades)): ?>
             <?php foreach ($bloques_novedades as $bloque): ?>
                 <div class="carrusel-bloque">  
                     <?php foreach ($bloque as $item): 
                         $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/57.png';
                     ?>
                         <div class="item-div"><br>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="item_desc">
                            <p><?php echo htmlspecialchars($item['nombre_prod']); ?></p>
                            <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto" target="_blank"></a>
                         </div>
                     <?php endforeach; ?>
                 </div>
             <?php endforeach; ?>
         <?php else: ?>
             <div class="carrusel-bloque">
                 <p style="text-align:center; width:100%; color:#666;">No hay novedades por el momento.</p>
             </div>
         <?php endif; ?>
      </div>
      <div class="carrusel-dots"></div>
   </div>
    
   <br><br>

   <div class="populares">  
      <p>PRODUCTOS POPULARES</p>
      <a href="productos_populares.php" class="enlace-populares" target="_blank"></a>
   </div>
   <br><br><br>
  
   <!-- CARRUSEL 2: PRODUCTOS POPULARES -->
   <div class="carrusel-contenedor">
      <button class="carrusel-btn prev" onclick="cambiarBloque(this, -1)">&#10094;</button>
      <button class="carrusel-btn next" onclick="cambiarBloque(this, 1)">&#10095;</button>
      
      <div class="carrusel-track">
         <?php if (!empty($bloques_populares)): ?>
             <?php foreach ($bloques_populares as $bloque): ?>
                 <div class="carrusel-bloque">  
                     <?php foreach ($bloque as $item): 
                         $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/57.png';
                     ?>
                         <div class="item-div"><br>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="item_desc">
                            <p><?php echo htmlspecialchars($item['nombre_prod']); ?></p>
                            <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto" target="_blank"></a>
                         </div>
                     <?php endforeach; ?>
                 </div>
             <?php endforeach; ?>
         <?php else: ?>
             <div class="carrusel-bloque">
                 <p style="text-align:center; width:100%; color:#666;">No hay productos populares disponibles.</p>
             </div>
         <?php endif; ?>
      </div>
      <div class="carrusel-dots"></div>
   </div>

   <br><br>
   
   <div class="promociones">  
      <p>PROMOCIONES ACTUALES</p>
      <a href="promociones_actuales.php" class="enlace-promociones" target="_blank"></a>
   </div>
   <br><br><br>
   
   <!-- CARRUSEL 3: PROMOCIONES ACTUALES CON SELLO DE DESCUENTO -->
   <div class="carrusel-contenedor">
      <button class="carrusel-btn prev" onclick="cambiarBloque(this, -1)">&#10094;</button>
      <button class="carrusel-btn next" onclick="cambiarBloque(this, 1)">&#10095;</button>
      
      <div class="carrusel-track">
         <?php if (!empty($bloques_promos)): ?>
             <?php foreach ($bloques_promos as $bloque): ?>
                 <div class="carrusel-bloque">  
                     <?php foreach ($bloque as $item): 
                         $img_src = !empty($item['imagen_frontal']) ? $item['imagen_frontal'] : 'product_images/57.png';
                         $precio_original = $item['precio'];
                         $precio_descuento = $precio_original * (1 - ($item['descuento'] / 100));
                     ?>
                         <div class="item-div"><br>
                            <!-- Chapa roja de descuento en la esquina de la tarjeta dentro del carrusel -->
                            <div class="sello-descuento">-<?php echo $item['descuento']; ?>%</div>
                            
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Producto" class="item_desc">
                            
                            <p style="line-height:1.2;">
                                <?php echo htmlspecialchars($item['nombre_prod']); ?><br>
                                
                            </p>
                            <a href="producto.php?id=<?php echo $item['id_producto']; ?>" class="enlace-producto" target="_blank"></a>
                         </div>
                     <?php endforeach; ?>
                 </div>
             <?php endforeach; ?>
         <?php else: ?>
             <div class="carrusel-bloque">
                 <p style="text-align:center; width:100%; color:#666;">No hay promociones disponibles en este momento.</p>
             </div>
         <?php endif; ?>
      </div>
      <div class="carrusel-dots"></div>
   </div>

   <br><br>

  
       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
   <script src="js/modal.js" defer></script>
</body>
</html>